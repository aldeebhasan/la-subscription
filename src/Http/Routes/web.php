<?php

use Illuminate\Support\Facades\Route;

Route::prefix("subscriptions")
    ->middleware(config('subscription.middleware'))
    ->name('subscriptions')
    ->group(function () {
        Route::get('{view?}', fn() => view('la-subscription::app'))
            ->where('view', '.*')
            ->name('index');
    });
