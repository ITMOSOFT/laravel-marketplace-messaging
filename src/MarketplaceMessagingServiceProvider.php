<?php

namespace ITMOSOFT\MarketplaceMessaging;

use Illuminate\Support\ServiceProvider;

class MarketplaceMessagingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/marketplace-messaging.php', 'itmosoft-marketplace-messaging');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/marketplace-messaging.php' => config_path('itmosoft-marketplace-messaging.php'),
        ], 'marketplace-messaging-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
