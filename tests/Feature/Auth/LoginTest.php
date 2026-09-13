<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Illuminate\Support\Facades\RateLimiter;
use Innoboxrr\LaravelAuth\Tests\TestCase;

/**
 * Los tests anteriores usaban la anotación `@test`, que PHPUnit 12 ya no lee:
 * el paquete se publicaba sin que el login se probara nunca.
 */
final class LoginTest extends TestCase
{
    public function test_un_usuario_inicia_sesion_con_su_correo_y_contrasena(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_con_una_contrasena_equivocada_responde_422_y_no_inicia_sesion(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('login'), ['email' => $user->email, 'password' => 'otra'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }

    public function test_tras_cinco_intentos_fallidos_frena_aunque_la_contrasena_sea_buena(): void
    {
        $user = $this->createUser();

        RateLimiter::clear(strtolower($user->email).'|127.0.0.1');

        foreach (range(1, 5) as $attempt) {
            $this->postJson($this->authUri('login'), ['email' => $user->email, 'password' => 'otra'])->assertUnprocessable();
        }

        $this->postJson($this->authUri('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertGuest();
    }
}
