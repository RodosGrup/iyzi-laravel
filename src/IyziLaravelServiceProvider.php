<?php

namespace RodosGrup\IyziLaravel;

use Illuminate\Support\ServiceProvider;

class IyziLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '../config/iyzi-laravel.php' => config_path('iyzi-laravel.php')
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/iyzi-laravel.php',
            'iyzi-laravel'
        );

        $this->app->bind('iyzico', function () {
            return new IyziLaravel();
        });
    }
}
