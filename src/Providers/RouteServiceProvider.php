<?php

namespace Innoboxrr\LaravelAuth\Providers;

use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Hereda de ServiceProvider y no del RouteServiceProvider de Foundation: ese
 * vuelve a ejecutar al arrancar el callback de `withRouting()` de la
 * aplicación, y la aplicación registraba sus rutas una vez más por cada paquete.
 */
class RouteServiceProvider extends ServiceProvider
{

    public function boot(): void
    {

        if ($this->app instanceof CachesRoutes && $this->app->routesAreCached()) {

            return;

        }

        if (config('laravel-auth.routes.active')) {

            $this->mapAuthRoutes();

        }

    }

    protected function mapAuthRoutes(): void
    {
        Route::middleware('web')
            ->as(config('laravel-auth.routes.as'))
            ->prefix(config('laravel-auth.routes.prefix'))
            ->namespace('Innoboxrr\LaravelAuth\Http\Controllers')
            ->group(__DIR__.'/../../routes/auth.php');
    }

}
