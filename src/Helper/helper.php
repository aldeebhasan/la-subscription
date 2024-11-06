<?php

use Aldeebhasan\LaSubscription\Models\Subscription;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
            if (DB::getDriverName() === 'sqlite') {
                return DB::raw(sprintf('date(end_at, "+%d days")', $graceDays));
            } else {
                return DB::raw(sprintf("DATE_ADD(end_at, INTERVAL %d DAY)", $graceDays));
            }
        }

        return "end_at";
    }
}

if (!function_exists('carbonParse')) {
    function carbonParse(mixed $datetime): CarbonInterface
    {
        return Carbon::parse($datetime);
    }
}
