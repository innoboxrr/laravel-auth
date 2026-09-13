<?php

namespace Innoboxrr\LaravelAuth\Tests\Feature\Auth;

use Innoboxrr\LaravelAuth\Tests\TestCase;

/**
 * Tokens de Sanctum para integraciones que no tienen sesión.
 *
 * Crear un token hacía `Auth::attempt`, que además inicia sesión en el
 * navegador; con credenciales malas no devolvía nada, un 200 vacío. Y leía la
 * caducidad de `expires_at` aunque la regla validaba `expiration_date`.
 */
final class TokenTest extends TestCase
{
    public function test_crea_un_token_que_sirve_para_la_api_sin_iniciar_sesion(): void
    {
        $user = $this->createUser();

        $token = $this->postJson($this->authUri('create-token'), [
            'email' => $user->email,
            'password' => 'password',
            'name' => 'ci',
        ])->assertOk()->json('token');

        $this->assertIsString($token);
        $this->assertGuest('web');

        $this->withToken($token)->postJson($this->authUri('tokens'))->assertOk()->assertJsonCount(1, 'tokens');
    }

    public function test_con_credenciales_invalidas_responde_422(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('create-token'), [
            'email' => $user->email,
            'password' => 'otra',
            'name' => 'ci',
        ])->assertUnprocessable()->assertJsonValidationErrors(['email']);

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_guarda_la_caducidad_del_token(): void
    {
        $user = $this->createUser();

        $this->postJson($this->authUri('create-token'), [
            'email' => $user->email,
            'password' => 'password',
            'name' => 'ci',
            'expires_at' => now()->addDay()->toDateTimeString(),
        ])->assertOk();

        $this->assertNotNull($user->tokens()->first()->expires_at);
    }

    public function test_revocar_un_token_propio(): void
    {
        $user = $this->createUser();
        $user->createToken('uno');

        $this->actingAs($user)
            ->postJson($this->authUri('revoke-token'), ['token_id' => $user->tokens()->first()->getKey()])
            ->assertOk()
            ->assertJson(['revoked' => true]);

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_no_se_revoca_el_token_de_otro_usuario(): void
    {
        $owner = $this->createUser();
        $owner->createToken('uno');

        $this->actingAs($this->createUser(['email' => 'otro@example.com']))
            ->postJson($this->authUri('revoke-token'), ['token_id' => $owner->tokens()->first()->getKey()])
            ->assertOk()
            ->assertJson(['revoked' => false]);

        $this->assertSame(1, $owner->tokens()->count());
    }

    public function test_revocar_todos(): void
    {
        $user = $this->createUser();
        $user->createToken('uno');
        $user->createToken('dos');

        $this->actingAs($user)->postJson($this->authUri('flush-tokens'))->assertOk()->assertJson(['success' => true]);

        $this->assertSame(0, $user->tokens()->count());
    }
}
