<?php

namespace Aldeebhasan\LaSubscription;

use Illuminate\Support\ServiceProvider;

class LaSubscriptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/subscription.php' => config_path('subscription.php'),
        ], 'la-subscription-config');

        $this->publishesMigrations([
            __DIR__ . '/../database/migrations' => database_path("migrations"),
        ], 'la-subscription-migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/subscription.php', 'subscription');
    }
}
