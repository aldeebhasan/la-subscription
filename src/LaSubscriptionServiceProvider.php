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

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path("migrations"),
        ], 'la-subscription-migrations');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/la-subscription'),
        ], 'la-subscription-views');

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/la-subscription'),
        ], 'public');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/subscription.php', 'subscription');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'la-subscription');
        $this->loadRoutesFrom(__DIR__ . '/../src/Http/Routes/web.php');
    }
}
