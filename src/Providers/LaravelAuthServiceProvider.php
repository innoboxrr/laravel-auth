<?php

namespace Innoboxrr\LaravelAuth\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelAuthServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->mergeConfigFrom(__DIR__.'/../../config/laravel-auth.php', 'laravel-auth');

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'laravel-auth');

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__.'/../../config/laravel-auth.php' => config_path('laravel-auth.php'),
            ], 'config');

        }

    }
}
