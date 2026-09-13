<?php

namespace Innoboxrr\LaravelAuth\Tests;

use Illuminate\Support\Facades\Hash;
use Innoboxrr\LaravelAuth\Providers\EmailVerificationServiceProvider;
use Innoboxrr\LaravelAuth\Providers\LaravelAuthServiceProvider;
use Innoboxrr\LaravelAuth\Providers\ResetPasswordServiceProvider;
use Innoboxrr\LaravelAuth\Providers\RouteServiceProvider;
use Innoboxrr\LaravelAuth\Tests\App\Models\User;
use Laravel\Sanctum\SanctumServiceProvider;
use Laravel\Socialite\SocialiteServiceProvider;
use Orchestra\Testbench\TestCase as Testbench;

/**
 * Una aplicación con los proveedores del paquete, Sanctum y un usuario que
 * verifica su correo, usa tokens y sabe si es administrador.
 */
abstract class TestCase extends Testbench
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);

        // Sanctum ya no carga su migración solo: se publica. Aquí se usa la
        // del vendor para que los tests de tokens tengan su tabla.
        $this->loadMigrationsFrom(dirname(__DIR__).'/vendor/laravel/sanctum/database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            SanctumServiceProvider::class,
            // En una aplicación llega por descubrimiento; Testbench no descubre.
            SocialiteServiceProvider::class,
            LaravelAuthServiceProvider::class,
            RouteServiceProvider::class,
            EmailVerificationServiceProvider::class,
            ResetPasswordServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // La sesión y los tokens de suplantación se cifran.
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('k', 32)));
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('laravel-auth.user-class', User::class);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ], $attributes));
    }

    protected function createAdmin(array $attributes = []): User
    {
        return $this->createUser(['name' => 'Admin', 'email' => 'admin@example.com', ...$attributes]);
    }

    /**
     * La URI de una ruta del paquete, tal como la configura quien lo instala.
     */
    protected function authUri(string $name): string
    {
        return '/'.config('laravel-auth.routes.prefix').'/'.config("laravel-auth.routes.uris.{$name}");
    }
}
