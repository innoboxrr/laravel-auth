<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Illuminate\Support\Facades\Hash;
use Innoboxrr\LaravelAuth\Tests\App\Models\User;
use Innoboxrr\LaravelAuth\Tests\TestCase;

final class RegisterTest extends TestCase
{
    public function test_registrarse_crea_la_cuenta_e_inicia_sesion(): void
    {
        $this->postJson($this->authUri('register'), [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'unaClaveLarga2026',
            'password_confirmation' => 'unaClaveLarga2026',
        ])->assertOk()->assertJson(['success' => true]);

        $user = User::where('email', 'ana@example.com')->firstOrFail();

        $this->assertTrue(Hash::check('unaClaveLarga2026', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_un_correo_ya_registrado_responde_422(): void
    {
        $this->createUser(['email' => 'ana@example.com']);

        $this->postJson($this->authUri('register'), [
            'name' => 'Otra Ana',
            'email' => 'ana@example.com',
            'password' => 'unaClaveLarga2026',
            'password_confirmation' => 'unaClaveLarga2026',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Otra Ana']);
    }

    public function test_la_confirmacion_tiene_que_coincidir(): void
    {
        $this->postJson($this->authUri('register'), [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'unaClaveLarga2026',
            'password_confirmation' => 'otraCosa',
        ])->assertUnprocessable()->assertJsonValidationErrors(['password']);
    }

    public function test_con_el_registro_cerrado_responde_403(): void
    {
        config(['laravel-auth.allow-registration' => false]);

        $this->postJson($this->authUri('register'), [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'unaClaveLarga2026',
            'password_confirmation' => 'unaClaveLarga2026',
        ])->assertForbidden();
    }
}
