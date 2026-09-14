<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Gate;
use Innoboxrr\LaravelAuth\Tests\App\Models\User;
use Innoboxrr\LaravelAuth\Tests\TestCase;

/**
 * Antes cualquier usuario con sesión pedía un token para entrar como cualquier
 * otro, incluido un administrador: las rutas no tenían middleware y la única
 * comprobación era `allow-impersonate`, que por omisión vale true.
 */
final class ImpersonateTest extends TestCase
{
    public function test_un_invitado_no_puede_pedir_un_token(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('impersonate'), ['target_user_id' => $user->id])->assertUnauthorized();
    }

    public function test_un_usuario_normal_no_puede_suplantar_a_nadie(): void
    {
        $user = $this->createUser();
        $other = $this->createUser(['email' => 'otro@example.com']);

        $this->actingAs($user)
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $other->id])
            ->assertForbidden();
    }

    public function test_un_administrador_obtiene_un_token_para_entrar_como_un_usuario(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $this->actingAs($admin)
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $user->id])
            ->assertOk()
            ->assertJsonStructure(['token', 'url']);
    }

    public function test_nadie_se_suplanta_a_si_mismo(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $admin->id])
            ->assertForbidden();
    }

    public function test_un_usuario_que_no_existe_responde_422(): void
    {
        $this->actingAs($this->createAdmin())
            ->postJson($this->authUri('impersonate'), ['target_user_id' => 999999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['target_user_id']);
    }

    public function test_el_token_solo_sirve_a_quien_lo_pidio(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();
        $intruder = $this->createUser(['email' => 'intruso@example.com']);

        $token = $this->actingAs($admin)
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $user->id])
            ->json('token');

        $this->actingAs($intruder)->getJson($this->tokenUri($token))->assertForbidden();

        $this->assertNotEquals($user->getKey(), $this->app['auth']->guard('web')->id(), 'Con un token ajeno se entró como el usuario.');
    }

    public function test_el_token_caduca_a_los_dos_minutos(): void
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $token = $this->actingAs($admin)
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $user->id])
            ->json('token');

        $this->travel(3)->minutes();

        $this->actingAs($admin)->getJson($this->tokenUri($token))->assertForbidden();
    }

    public function test_con_el_token_el_administrador_entra_como_el_usuario_y_puede_volver(): void
    {
        [$admin] = $this->impersonating();

        $this->postJson($this->authUri('revert-impersonate'))->assertOk()->assertJson(['success' => true, 'user' => ['id' => $admin->id]]);

        $this->assertAuthenticatedAs($admin, 'web');
        $this->assertFalse(session()->has('impersonate_token'));
    }

    public function test_volver_sin_pedir_json_redirige(): void
    {
        [$admin] = $this->impersonating();

        $this->post($this->authUri('revert-impersonate'))->assertRedirect(config('laravel-auth.routes.redirects.revert-impersonate'));

        $this->assertAuthenticatedAs($admin, 'web');
    }

    /**
     * Volver cambia la cuenta de la sesión. Cuando era un GET, otro sitio lo
     * disparaba con un <img> o un enlace: el middleware CSRF no mira las
     * peticiones de lectura. Ya no hay ruta GET; en una aplicación responde
     * 405 o su fallback, pero nunca toca la sesión.
     */
    public function test_un_get_no_termina_la_suplantacion(): void
    {
        [, $user] = $this->impersonating();

        $this->get($this->authUri('revert-impersonate'))->assertMethodNotAllowed();

        $this->assertAuthenticatedAs($user, 'web');
        $this->assertTrue(session()->has('impersonate_token'), 'Un GET terminó la suplantación.');
    }

    public function test_un_post_de_otro_sitio_sin_token_csrf_no_termina_la_suplantacion(): void
    {
        [, $user] = $this->impersonating();

        $this->enforceCsrf();

        $this->withHeader('Sec-Fetch-Site', 'cross-site')
            ->post($this->authUri('revert-impersonate'))
            ->assertStatus(419);

        $this->assertAuthenticatedAs($user, 'web');
        $this->assertTrue(session()->has('impersonate_token'), 'Un POST sin token CSRF terminó la suplantación.');
    }

    /**
     * La otra mitad del test anterior: con el middleware CSRF activo, el token
     * de la sesión basta. Sin esto, el 419 podría venir de otra cosa.
     */
    public function test_un_post_con_el_token_csrf_de_la_sesion_termina_la_suplantacion(): void
    {
        [$admin] = $this->impersonating();

        $this->enforceCsrf();

        $this->withHeader('X-CSRF-TOKEN', session()->token())
            ->postJson($this->authUri('revert-impersonate'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertAuthenticatedAs($admin, 'web');
    }

    public function test_sin_suplantacion_en_curso_no_hay_nada_que_revertir(): void
    {
        $this->actingAs($this->createUser())
            ->postJson($this->authUri('revert-impersonate'))
            ->assertForbidden();
    }

    public function test_con_la_suplantacion_desactivada_nadie_puede(): void
    {
        config(['laravel-auth.allow-impersonate' => false]);

        $this->actingAs($this->createAdmin())
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $this->createUser()->id])
            ->assertForbidden();
    }

    /**
     * Quién suplanta lo decide una habilidad de Gate, y la aplicación la
     * redefine en su AuthServiceProvider.
     */
    public function test_la_aplicacion_decide_quien_suplanta_con_una_habilidad(): void
    {
        Gate::define('laravel-auth.impersonate', fn ($user, $target = null): bool => $user->email === 'soporte@example.com');

        $support = $this->createUser(['email' => 'soporte@example.com']);
        $user = $this->createUser();

        $this->actingAs($support)
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $user->id])
            ->assertOk();

        $this->actingAs($this->createAdmin())
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $user->id])
            ->assertForbidden();
    }

    /**
     * Un administrador que ya entró como un usuario con el token.
     *
     * @return array{0: User, 1: User}
     */
    private function impersonating(): array
    {
        $admin = $this->createAdmin();
        $user = $this->createUser();

        $token = $this->actingAs($admin)
            ->postJson($this->authUri('impersonate'), ['target_user_id' => $user->id])
            ->json('token');

        $this->getJson($this->tokenUri($token))->assertOk()->assertJson(['success' => true]);

        // El paquete inicia sesión en el guard web. En un test, el guard de
        // Sanctum conserva el usuario que resolvió en la petición anterior;
        // en una aplicación cada petición empieza de cero.
        $this->assertAuthenticatedAs($user, 'web');
        $this->assertTrue(session()->has('impersonate_token'));

        return [$admin, $user];
    }

    /**
     * Laravel no comprueba el token CSRF mientras corren los tests
     * (PreventRequestForgery::runningUnitTests()), así que un POST sin token
     * pasaría igual. Aquí el middleware del grupo `web` se comporta como en una
     * aplicación.
     */
    private function enforceCsrf(): void
    {
        $this->app->bind(PreventRequestForgery::class, fn ($app) => new class($app, $app['encrypter']) extends PreventRequestForgery
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });
    }

    private function tokenUri(string $token): string
    {
        return route(config('laravel-auth.routes.as').config('laravel-auth.routes.names.impersonate-token'), ['token' => $token], false);
    }
}
