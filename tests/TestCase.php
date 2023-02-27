<?php

namespace Innoboxrr\LaravelAuth\Tests;

use Innoboxrr\LaravelAuth\Providers\LaravelAuthServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp(): void
    {
        
        parent::setUp();
        // additional setup

    }

    protected function getPackageProviders($app)
    {
        
        return [
            LaravelAuthServiceProvider::class
        ];

    }

    protected function getEnvironmentSetUp($app)
    {
        
        // perform environment setup

    }

}