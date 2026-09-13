<?php

namespace Innoboxrr\LaravelAuth\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class ResetPasswordServiceProvider extends ServiceProvider
{
    /**
     * El correo de restablecer contraseña enlaza a la pantalla de la aplicación,
     * no a la ruta de la API.
     *
     * La ruta sale de `laravel-auth.frontend.reset-password`, y el correo va
     * codificado: antes iba tal cual, y una dirección con `+` llegaba cambiada.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($notifiable, string $token): string {
            $path = config('laravel-auth.frontend.reset-password', 'auth/reset-password/{token}/{email}');

            return url(strtr($path, [
                '{token}' => $token,
                '{email}' => rawurlencode($notifiable->getEmailForPasswordReset()),
            ]));
        });
    }
}
