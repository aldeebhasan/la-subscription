<?php

namespace Aldeebhasan\LaSubscription;

use Illuminate\Support\ServiceProvider;

class LaSubscriptionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/subscription.php' => config_path('subscription.php'),
        ], 'la-subscription');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/subscription.php',
            'la-subscription'
        );
    }
}
