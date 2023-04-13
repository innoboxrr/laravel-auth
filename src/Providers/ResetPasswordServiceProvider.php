<?php

namespace Innoboxrr\LaravelAuth\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class ResetPasswordServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        
        ResetPassword::createUrlUsing(function($notifiable, $token) {

            // PENDIENTE: Mandar la URI a config
            return url('reset-password', [
                        'token' => $token,
                        'email' => $notifiable->getEmailForPasswordReset(),
                    ]);

        });
        
    }

}
