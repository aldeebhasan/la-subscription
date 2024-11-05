<?php

namespace Aldeebhasan\LaSubscription;

use Illuminate\Support\ServiceProvider;

use function Orchestra\Testbench\default_migration_path;

class LaSubscriptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/subscription.php' => config_path('subscription.php'),
        ], 'la-subscription-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => default_migration_path(),
        ], 'la-subscription-migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/subscription.php', 'subscription');
    }
}
