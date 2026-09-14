<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Innoboxrr\LaravelAuth\Tests\App\Models\UserWithoutVerification;
use Innoboxrr\LaravelAuth\Tests\TestCase;

/**
 * Lo que una SPA le pregunta al paquete una vez identificada: quién es, si
 * administra, si verificó su correo, y cómo cambia su contraseña o sale.
 */
final class AccountTest extends TestCase
{
    public function test_sin_sesion_get_auth_dice_que_no_hay_usuario(): void
    {
        $this->getJson($this->authUri('get-auth'))
            ->assertOk()
            ->assertJson(['user' => null, 'authenticated' => false, 'is_admin' => false, 'verified' => false]);
    }

    public function test_get_auth_dice_quien_es_si_administra_y_si_verifico_su_correo(): void
    {
        $this->actingAs($this->createAdmin())
            ->getJson($this->authUri('get-auth'))
            ->assertOk()
            ->assertJson(['authenticated' => true, 'is_admin' => true, 'verified' => false, 'impersonating' => false])
            ->assertJsonPath('user.email', 'admin@example.com')
            ->assertJsonMissingPath('user.password');
    }

    /**
     * Laravel sólo envía el correo de verificación a quien implementa
     * MustVerifyEmail. Responder `verified: false` a los demás hacía que la SPA
     * les pidiera verificar un correo que nunca iba a llegar.
     */
    public function test_un_usuario_sin_verificacion_de_correo_cuenta_como_verificado(): void
    {
        $user = UserWithoutVerification::forceCreate([
            'name' => 'Sin verificación',
            'email' => 'sin-verificacion@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->assertNull($user->email_verified_at);

        $this->actingAs($user)
            ->getJson($this->authUri('get-auth'))
            ->assertOk()
            ->assertJson(['authenticated' => true, 'verified' => true]);
    }

    public function test_el_login_devuelve_el_usuario(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('user.email', $user->email);
    }

    public function test_cerrar_sesion(): void
    {
        $this->actingAs($this->createUser(), 'web')
            ->postJson($this->authUri('logout'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertGuest('web');
    }

    public function test_cambiar_la_contrasena_exige_la_actual(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->postJson($this->authUri('update-password'), [
                'old_password' => 'otra',
                'password' => 'NuevaClave2026',
                'password_confirmation' => 'NuevaClave2026',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['old_password']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_cambiar_la_contrasena(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->postJson($this->authUri('update-password'), [
                'old_password' => 'password',
                'password' => 'NuevaClave2026',
                'password_confirmation' => 'NuevaClave2026',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('NuevaClave2026', $user->fresh()->password));
    }

    /**
     * AuthenticateSession compara la huella de la contraseña guardada en la
     * sesión; si el cambio no la actualiza, la siguiente petición de la SPA a
     * la API cierra la sesión (lo encontró el piloto de la aplicación base).
     */
    public function test_cambiar_la_contrasena_no_cierra_la_sesion(): void
    {
        $user = $this->createUser();
        $guard = auth()->guard('web');

        $this->actingAs($user, 'web')
            ->withSession(['password_hash_web' => $guard->hashPasswordForCookie($user->getAuthPassword())])
            ->postJson($this->authUri('update-password'), [
                'old_password' => 'password',
                'password' => 'NuevaClave2026',
                'password_confirmation' => 'NuevaClave2026',
            ])
            ->assertOk()
            ->assertSessionHas('password_hash_web', $guard->hashPasswordForCookie($user->fresh()->getAuthPassword()));
    }

    public function test_un_invitado_no_cambia_contrasenas(): void
    {
        $this->postJson($this->authUri('update-password'), [
            'old_password' => 'password',
            'password' => 'NuevaClave2026',
            'password_confirmation' => 'NuevaClave2026',
        ])->assertUnauthorized();
    }

    public function test_reenviar_el_correo_de_verificacion(): void
    {
        Notification::fake();

        $user = $this->createUser();

        $this->actingAs($user)
            ->postJson($this->authUri('email-verification-notification'))
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'verification-link-sent']);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_con_el_correo_ya_verificado_no_se_reenvia(): void
    {
        Notification::fake();

        $user = $this->createUser();
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->actingAs($user)
            ->postJson($this->authUri('email-verification-notification'))
            ->assertOk()
            ->assertJson(['success' => true, 'status' => 'already-verified']);

        Notification::assertNothingSent();
    }

    public function test_el_enlace_firmado_verifica_el_correo(): void
    {
        $user = $this->createUser();

        $url = URL::temporarySignedRoute('auth.verification.verify', now()->addHour(), [
            'id' => $user->getKey(),
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)->getJson($url)->assertOk()->assertJson(['verified' => true]);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
