<?php

use Aldeebhasan\LaSubscription\Http\Controllers\FeatureController;
use Aldeebhasan\LaSubscription\Http\Controllers\GroupController;
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
            RouteManager::make()->ncResource('groups', GroupController::class);
            Route::get('plans/create', [PlanController::class, 'create'])->name('plans.create');
            Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
            RouteManager::make()->ncResource('plans', PlanController::class);
            Route::get('plugins/create', [PluginController::class, 'create'])->name('plugins.create');
            Route::get('plugins/{plugin}/edit', [PluginController::class, 'edit'])->name('plugins.edit');
            RouteManager::make()->ncResource('plugins', PluginController::class);
            Route::get('features/create', [FeatureController::class, 'create'])->name('features.create');
            Route::get('features/{feature}/edit', [FeatureController::class, 'edit'])->name('features.edit');
            RouteManager::make()->ncResource('features', FeatureController::class);
            RouteManager::make()->ncResource('subscriptions', SubscriptionController::class);
        });

        Route::get('{view?}', fn() => view('la-subscription::app'))
            ->where('view', '.*')
            ->name('index');
    });
