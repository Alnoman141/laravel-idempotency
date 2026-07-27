<?php

namespace alnoman141\LaravelIdempotency;

use Illuminate\Support\ServiceProvider;
use alnoman141\LaravelIdempotency\Stores\CacheStore;
use alnoman141\LaravelIdempotency\Contracts\IdempotencyStore;
use alnoman141\LaravelIdempotency\Contracts\IdempotencyLock;
use alnoman141\LaravelIdempotency\Locks\CacheLock;
use alnoman141\LaravelIdempotency\Support\ResponseStoragePolicy;
use alnoman141\LaravelIdempotency\Support\MiddlewareOptions;
use alnoman141\LaravelIdempotency\Support\RequestFingerprint;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use alnoman141\LaravelIdempotency\Http\Middleware\IdempotencyMiddleware;
use alnoman141\LaravelIdempotency\Contracts\FlushableStore;

class IdempotencyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/idempotency.php',
            'idempotency'
        );

        $this->app->singleton(
            IdempotencyStoreManager::class
        );

        $this->app->bind(
            IdempotencyStore::class,
            function ($app) {
                return $app
                    ->make(IdempotencyStoreManager::class)
                    ->driver();
            }
        );

        $this->app->singleton(
            IdempotencyLock::class,
            CacheLock::class
        );

        $this->app->singleton(
            RequestFingerprint::class
        );

        $this->app->singleton(ResponseStoragePolicy::class, function () {
            return new ResponseStoragePolicy();
        });

        $this->app->singleton(MiddlewareOptions::class, function () {
            return new MiddlewareOptions();
        });
    }

    public function boot(): void
    {
        // Publish Config
        $this->publishes([
            __DIR__ . '/../config/idempotency.php'
            => config_path('idempotency.php'),
        ], 'idempotency-config');

        // Publish Migration
        $this->publishesMigrations([
            __DIR__ . '/../database/migrations'
            => database_path('migrations'),
        ]);

        // Register Middleware Alias
        $this->app
            ->make(Router::class)
            ->aliasMiddleware(
                'idempotency',
                IdempotencyMiddleware::class
            );

        Route::macro('idempotent', function () {
            return $this->middleware('idempotency');
        });

        if ($this->app->runningInConsole()) {

            $this->commands([
                \alnoman141\LaravelIdempotency\Console\ClearIdempotencyCommand::class,
                \alnoman141\LaravelIdempotency\Console\StatsCommand::class,
                \alnoman141\LaravelIdempotency\Console\PruneCommand::class,
            ]);
        }

        $this->app->bind(
            FlushableStore::class,
            function ($app) {

                return $app
                    ->make(IdempotencyStoreManager::class)
                    ->driver();
            }
        );
    }
}

