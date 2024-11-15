<?php

// config for Aldeebhasan/LaSubscription
return [
    'prefix' => env("LA_SUBSCRIPTION_PREFIX", "la"),
    'grace_period' => env("LA_GRACE_PERIOD", 7),
    'path' => env("LA_PATH", 'subscriptions'),
    'middleware' => ['web'],
];
