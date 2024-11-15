<?php

use Aldeebhasan\LaSubscription\Http\Controllers\FeatureController;
use Aldeebhasan\LaSubscription\Http\Controllers\PlanController;
use Aldeebhasan\LaSubscription\Http\Controllers\PluginController;
use Aldeebhasan\LaSubscription\Http\Controllers\SubscriptionController;
use Aldeebhasan\NaiveCrud\Logic\Managers\RouteManager;
use Illuminate\Support\Facades\Route;

Route::prefix("subscriptions")
    ->middleware(config('subscription.middleware'))
    ->name('subscriptions')
    ->group(function () {
        Route::prefix('api')->group(function () {
            RouteManager::make()->ncResource('plans', PlanController::class);
            RouteManager::make()->ncResource('plugins', PluginController::class);
            RouteManager::make()->ncResource('features', FeatureController::class);
            RouteManager::make()->ncResource('subscriptions', SubscriptionController::class);
        });

        Route::get('{view?}', fn() => view('la-subscription::app'))
            ->where('view', '.*')
            ->name('index');
    });
