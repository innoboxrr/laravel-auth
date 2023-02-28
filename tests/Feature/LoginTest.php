<?php

namespace Tests\Feature;

use Innoboxrr\LaravelAuth\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\RateLimiter;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login()
    {

        $routeCollection = \Illuminate\Support\Facades\Route::getRoutes();

        $user = $this->createUser();

        $response = $this->postJson(config('laravel-auth.routes.prefix') . '/' .  config('laravel-auth.routes.uris.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {

        $user = $this->createUser();

        $response = $this->postJson(config('laravel-auth.routes.prefix') . '/' .  config('laravel-auth.routes.uris.login'), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'These credentials do not match our records.',
            ]);
    }

    /** @test */
    public function user_can_login_with_valid_credentials_after_throttle_is_expired()
    {
        $this->withoutExceptionHandling();

        $user = $this->createUser();

        RateLimiter::hit('login.' . $user->email, 5);

        $response = $this->postJson(config('laravel-auth.routes.prefix') . '/' .  config('laravel-auth.routes.uris.login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertAuthenticatedAs($user);
    }

    // ...
}