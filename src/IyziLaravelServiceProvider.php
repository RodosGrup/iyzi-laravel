<?php

namespace RodosGrup\IyziLaravel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RodosGrup\IyziLaravel\Contracts\IyzicoUser as IyzicoUserContract;
use RodosGrup\IyziLaravel\Contracts\StoredCreditCard as StoredCreditCardContract;

class IyziLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();
        $this->registerModelBindings();

        $this->publishes([
            __DIR__ . '/../config/iyzi-laravel.php' => config_path('iyzi-laravel.php')
        ]);

        $this->publishes([
            __DIR__ . '/../src/Models/IyzicoUser.php' => base_path('app/Models/IyzicoUser.php'),
            __DIR__ . '/../src/Models/StoredCreditCard.php' => base_path('app/Models/StoredCreditCard.php')
        ]);

        RedirectResponse::macro('payment', function ($payment) {
            return $this->with('payment', $payment);
        });
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

    protected function offerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_iyzico_users_table.php.stub' => $this->getMigrationFileName('create_iyzico_users_table.php'),
            __DIR__ . '/../database/migrations/create_stored_credit_cards_tables.php.stub' => $this->getMigrationFileName('create_stored_credit_cards_tables.php')
        ], 'iyzico-users-and-credit-card-migrations');
    }

    protected function registerModelBindings(): void
    {
        $this->app->bind(IyzicoUserContract::class, fn ($app) => $app->make($app->config['iyzi-laravel.model.user']));
        $this->app->bind(StoredCreditCardContract::class, fn ($app) => $app->make($app->config['iyzi-laravel.model.cards']));
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

    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesytem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesytem->glob($path . '*_' . $migrationFileName))
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
