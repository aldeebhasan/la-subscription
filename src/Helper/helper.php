<?php

use Aldeebhasan\LaSubscription\Models\Subscription;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;


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


if (!function_exists('css')) {
    function css(): Htmlable
    {
        if (($light = @file_get_contents(__DIR__ . '/../../dist/styles.css')) === false) {
            throw new RuntimeException('Unable to load the dashboard light CSS.');
        }

        if (($app = @file_get_contents(__DIR__ . '/../../dist/app.css')) === false) {
            throw new RuntimeException('Unable to load the dashboard CSS.');
        }

        return new HtmlString(<<<HTML
            <style data-scheme="light">{$light}</style>
            <style>{$app}</style>
            HTML
        );
    }
}
if (!function_exists('js')) {
    function js(): Htmlable
    {
        if (($js = @file_get_contents(__DIR__ . '/../../dist/app.js')) === false) {
            throw new RuntimeException('Unable to load the  dashboard JavaScript.');
        }

        return new HtmlString(<<<HTML
            <script type="module">
                {$js}
            </script>
            HTML
        );
    }
}
