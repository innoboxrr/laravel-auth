<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Innoboxrr\LaravelAuth\Tests\TestCase;

/**
 * Las reglas de contraseña vivían en `routes.password` y la regla las buscaba en
 * `password`: nunca se aplicaban, y el mensaje decía "8 caracteres, una
 * mayúscula y un número" aunque no se exigiera nada de eso.
 */
final class PasswordRulesTest extends TestCase
{
    public function test_la_longitud_configurada_se_aplica_al_registrarse(): void
    {
        config(['laravel-auth.password.length' => 12]);

        $this->register('corta2026')->assertUnprocessable()->assertJsonValidationErrors(['password']);
        $this->register('suficienteLarga')->assertOk();
    }

    public function test_la_configuracion_antigua_bajo_routes_sigue_valiendo(): void
    {
        config([
            'laravel-auth.password' => [],
            'laravel-auth.routes.password' => ['length' => 12],
        ]);

        $this->register('corta2026')->assertUnprocessable()->assertJsonValidationErrors(['password']);
    }

    public function test_puede_exigir_una_mayuscula_y_un_numero(): void
    {
        config(['laravel-auth.password.uppercase' => true, 'laravel-auth.password.number' => true]);

        $this->register('todominusculas')->assertUnprocessable()->assertJsonValidationErrors(['password']);
        $this->register('SinNumeroAlguno')->assertUnprocessable()->assertJsonValidationErrors(['password']);
        $this->register('ConNumero2026')->assertOk();
    }

    public function test_el_mensaje_dice_la_regla_que_falla_en_el_idioma_de_la_aplicacion(): void
    {
        config(['laravel-auth.password.length' => 12]);

        app()->setLocale('es');

        $message = $this->register('corta2026')->json('errors.password.0');

        $this->assertStringContainsString('12', $message);
        $this->assertStringContainsString('caracteres', $message);
    }

    private function register(string $password): \Illuminate\Testing\TestResponse
    {
        return $this->postJson($this->authUri('register'), [
            'name' => 'Ana',
            'email' => 'ana'.uniqid().'@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);
    }
}
