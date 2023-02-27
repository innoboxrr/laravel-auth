<?php

namespace Innoboxrr\LaravelAuth\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    public function map()
    {

        if(config('laravel-auth.routes.active')) {

            $this->mapAuthRoutes();

        }

    }

    protected function mapAuthRoutes()
    {
        Route::middleware('web')
            ->as(config('laravel-auth.routes.as'))
            ->prefix(config('laravel-auth.routes.prefix'))
            ->namespace('Innoboxrr\LaravelAuth\Http\Controllers')
            ->group(__DIR__.'/../../routes/auth.php');
    }

}
