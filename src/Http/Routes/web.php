<?php

use Aldeebhasan\LaSubscription\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix("subscriptions")
    ->name('subscriptions')
    ->group(function () {
        Route::get('{view?}', fn() => view('la-subscription::app'))
            ->where('view', '.*')
            ->name('index');
    });
