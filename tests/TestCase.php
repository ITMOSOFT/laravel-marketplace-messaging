<?php

namespace ITMOSOFT\MarketplaceMessaging\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use ITMOSOFT\MarketplaceMessaging\MarketplaceMessagingServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [MarketplaceMessagingServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
    }
}
