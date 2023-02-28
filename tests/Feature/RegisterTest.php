<?php

namespace Tests\Feature;

use Innoboxrr\LaravelAuth\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;

class RegisterTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_register()
    {   

        $email = $this->faker->unique()->safeEmail;

        $response = $this->postJson(config('laravel-auth.routes.prefix') . '/' .  config('laravel-auth.routes.uris.register'), [
            'name' => 'John Doe',
            'email' => $email,
            'password' => 'testPassword2023',
            'password_confirmation' => 'testPassword2023',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    /** @test */
    public function user_cannot_register_with_existing_email()
    {

        $user = $this->createUser([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $response = $this->postJson(config('laravel-auth.routes.prefix') . '/' .  config('laravel-auth.routes.uris.register'), [
            'name' => 'Random name',
            'email' => 'john@example.com',
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertDatabaseMissing('users', [
            'name' => 'Random name',
        ]);
    }

}