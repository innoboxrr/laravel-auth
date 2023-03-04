<?php

namespace Innoboxrr\LaravelAuth\Tests;

use Innoboxrr\LaravelAuth\Tests\App\Models\User;
use Illuminate\Support\Facades\Hash;
use Innoboxrr\LaravelAuth\Providers\LaravelAuthServiceProvider;
use Innoboxrr\LaravelAuth\Providers\RouteServiceProvider;
use Laravel\Sanctum\SanctumServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    protected $password;

    public function setUp(): void
    {
        
        parent::setUp();
        // additional setup

        $this->loadLaravelMigrations(['--database' => 'testing']);
        
        $this->artisan('migrate', ['--database' => 'testing'])->run();

        $this->password = $this->getPassword();


    }

    protected function getPackageProviders($app)
    {
        
        return [
            LaravelAuthServiceProvider::class,
            RouteServiceProvider::class,
            SanctumServiceProvider::class,
        ];

    }

    protected function getEnvironmentSetUp($app)
    {
        
        // perform environment setup

        $app['config']->set('laravel-auth.user-class', User::class);

    }

    protected function createUser(array $attributes = [])
    {
        $defaults = [
            'id' => $this->getRandomDigit(),
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
        ];

        return User::create(array_merge($defaults, $attributes));
    }

    protected function getRandomDigit()
    {   

        // Genera 4 bytes aleatorios
        $randomBytes = random_bytes(4); 
        
        // Convierte los bytes a un número entero sin signo
        $randomNumber = unpack('L', $randomBytes)[1]; 

        return $randomNumber;

    }

    protected function getPassword()
    {

        return password_hash(substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 8)), 0, 8), PASSWORD_DEFAULT);

    }

}