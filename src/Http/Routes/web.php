<?php

use Aldeebhasan\LaSubscription\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix("subscriptions")
    ->name('subscriptions')
    ->group(function () {
        Route::redirect('', 'subscriptions/dashboard');
        Route::get('dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
    });
