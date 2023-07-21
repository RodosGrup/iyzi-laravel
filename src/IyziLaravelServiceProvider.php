<?php

namespace RodosGrup\IyziLaravel;

use Illuminate\Support\Facades\Route;
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
        $this->registerRoutes();

        $this->mergeConfigFrom(
            __DIR__ . '/../config/iyzi-laravel.php',
            'iyzi-laravel'
        );

        $this->app->bind('iyzico', function () {
            return new IyziLaravel();
        });
    }

    protected function registerRoutes()
    {
        Route::group([
            'prefix' => 'iyzi-laravel',
            'namespace' => 'RodosGrup\IyziLaravel\Http\Controllers',
            'middleware' => ['web', 'guest']
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}
