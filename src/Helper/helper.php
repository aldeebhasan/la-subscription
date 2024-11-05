<?php

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

if (!function_exists('subscription')) {
    function subscription(Model $model): ?Subscription
    {
        if (method_exists($model, 'getSubscription')) {
            return $model->getSubscription();
        }

        return null;
    }
}

if (!function_exists('gracedEndDateColumn')) {
    function gracedEndDateColumn(): Illuminate\Contracts\Database\Query\Expression|string
    {
        $graceDays = config('subscription.grace_period', 0);
        if ($graceDays > 0) {
            return DB::raw("DATE_ADD(end_at, INTERVAL {$graceDays} DAY)");
        }

        return "end_at";
    }
}
