<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Innoboxrr\LaravelAuth\Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    public function test_pedir_el_enlace_envia_el_correo(): void
    {
        Notification::fake();

        $user = $this->createUser();

        $this->postJson($this->authUri('forgot-password'), ['email' => $user->email])
            ->assertOk()
            ->assertJson(['success' => true]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Antes respondía "No pudimos encontrar un usuario con esa dirección": la
     * pantalla servía para averiguar qué correos tienen cuenta.
     */
    public function test_un_correo_sin_cuenta_recibe_la_misma_respuesta(): void
    {
        Notification::fake();

        $user = $this->createUser();

        $registered = $this->postJson($this->authUri('forgot-password'), ['email' => $user->email])->assertOk()->json();
        $unknown = $this->postJson($this->authUri('forgot-password'), ['email' => 'nadie@example.com'])->assertOk()->json();

        $this->assertSame($registered, $unknown);
    }

    /**
     * El enlace iba a `auth/reset-password/{token}/{email}` con el correo sin
     * codificar: una dirección con `+` llegaba cambiada a la pantalla.
     */
    public function test_el_enlace_lleva_a_la_pantalla_de_la_aplicacion_con_el_correo_codificado(): void
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'ana+test@example.com']);

        $this->postJson($this->authUri('forgot-password'), ['email' => $user->email])->assertOk();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $url = $notification->toMail($user)->actionUrl;

            return str_ends_with($url, '/auth/reset-password/'.$notification->token.'/'.rawurlencode('ana+test@example.com'));
        });
    }

    public function test_con_un_token_valido_cambia_la_contrasena(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('reset-password'), [
            'token' => Password::createToken($user),
            'email' => $user->email,
            'password' => 'NuevaClave2026',
            'password_confirmation' => 'NuevaClave2026',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('NuevaClave2026', $user->fresh()->password));
    }

    /**
     * Respondía 200 con `success: false`: la pantalla tenía que mirar el cuerpo
     * para saber que falló, y no traía el error del campo.
     */
    public function test_con_un_token_invalido_responde_422(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('reset-password'), [
            'token' => 'no-es-un-token',
            'email' => $user->email,
            'password' => 'NuevaClave2026',
            'password_confirmation' => 'NuevaClave2026',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
