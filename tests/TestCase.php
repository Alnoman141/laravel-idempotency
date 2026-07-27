<?php

namespace Alnoman141\LaravelIdempotency\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Alnoman141\LaravelIdempotency\IdempotencyServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            IdempotencyServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('idempotency.driver', 'cache');
        $app['config']->set('idempotency.ttl', 3600);
    }
}