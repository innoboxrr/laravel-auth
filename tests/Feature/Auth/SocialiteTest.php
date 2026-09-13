<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Innoboxrr\LaravelAuth\Tests\App\Models\User;
use Innoboxrr\LaravelAuth\Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

/**
 * Un proveedor sin credenciales en config/services.php hacía que Socialite
 * lanzara una excepción: la pantalla mostraba un 500 por un botón de "Entrar
 * con GitHub" que la aplicación nunca configuró.
 */
final class SocialiteTest extends TestCase
{
    public function test_un_proveedor_sin_configurar_responde_404(): void
    {
        $this->get('/auth/social/github/redirect')->assertNotFound();
        $this->get('/auth/social/github/callback')->assertNotFound();
    }

    public function test_un_proveedor_configurado_redirige_a_su_pantalla(): void
    {
        $this->configureGithub();

        $provider = Mockery::mock();
        $provider->shouldReceive('redirect')->andReturn(redirect('https://github.com/login/oauth/authorize'));

        Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

        $this->get('/auth/social/github/redirect')->assertRedirect('https://github.com/login/oauth/authorize');
    }

    public function test_la_vuelta_del_proveedor_crea_la_cuenta_e_inicia_sesion(): void
    {
        $this->configureGithub();

        $this->mockProviderUser(['id' => '42', 'name' => 'Ana', 'email' => 'ana@example.com']);

        $this->get('/auth/social/github/callback')->assertRedirect();

        $this->assertAuthenticatedAs(User::where('email', 'ana@example.com')->firstOrFail(), 'web');
    }

    public function test_la_vuelta_del_proveedor_entra_en_una_cuenta_existente(): void
    {
        $this->configureGithub();

        $user = $this->createUser(['email' => 'ana@example.com']);

        $this->mockProviderUser(['id' => '42', 'name' => 'Ana', 'email' => 'ana@example.com']);

        $this->get('/auth/social/github/callback')->assertRedirect();

        $this->assertAuthenticatedAs($user, 'web');
        $this->assertSame(1, User::count());
    }

    private function configureGithub(): void
    {
        config(['services.github' => [
            'client_id' => 'id',
            'client_secret' => 'secret',
            'redirect' => '/auth/social/github/callback',
        ]]);
    }

    /**
     * @param  array<string, string>  $attributes
     */
    private function mockProviderUser(array $attributes): void
    {
        $provider = Mockery::mock();
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn((new SocialiteUser())->map($attributes));

        Socialite::shouldReceive('driver')->with('github')->andReturn($provider);
    }
}
