<?php

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Model;

if (!function_exists('subscription')) {
    function subscription(Model $model): ?Subscription
    {
        if (method_exists($model, 'getSubscription')) {
            return $model->getSubscription();
        }

        return null;
    }
}
