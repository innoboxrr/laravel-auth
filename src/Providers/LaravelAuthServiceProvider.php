<?php

namespace Innoboxrr\LaravelAuth\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class LaravelAuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/laravel-auth.php', 'laravel-auth');
    }

    public function boot(): void
    {
        // Los mensajes son claves en inglés; la aplicación los traduce en su
        // lang/<idioma>.json, y el paquete trae el español.
        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');

        $this->defineImpersonateAbility();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/laravel-auth.php' => config_path('laravel-auth.php'),
            ], ['config', 'laravel-auth-config']);
        }
    }

    /**
     * Quién puede entrar como otro usuario.
     *
     * Antes no lo decidía nadie: cualquier usuario con sesión pedía un token
     * para cualquier otro. Por omisión, sólo un administrador —el modelo
     * responde true a isAdmin()—, nunca sobre sí mismo y nunca sobre otro
     * administrador. Si la aplicación ya definió la habilidad, manda la suya.
     */
    protected function defineImpersonateAbility(): void
    {
        if (Gate::has('laravel-auth.impersonate')) {
            return;
        }

        Gate::define('laravel-auth.impersonate', function (Authenticatable $user, ?Authenticatable $target = null): bool {
            if (! method_exists($user, 'isAdmin') || ! $user->isAdmin()) {
                return false;
            }

            if ($target === null) {
                return true;
            }

            if ((string) $target->getAuthIdentifier() === (string) $user->getAuthIdentifier()) {
                return false;
            }

            return ! (method_exists($target, 'isAdmin') && $target->isAdmin());
        });
    }
}
