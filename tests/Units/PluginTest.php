<?php

use Aldeebhasan\LaSubscription\Exceptions\SubscriptionRequiredExp;
use Aldeebhasan\LaSubscription\LaSubscription;
use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\LaSubscription\Tests\Sample\App\Models\User;

if (version_compare(\Pest\version(), "3.0.0") >= 0) {
    pest()->group('plugins');
}

it('can\'t add plugin with no subscription', function () {
    $subscriber = User::factory()->create();
    $plugin = Product::factory()->create();

    LaSubscription::make($subscriber)
        ->addPlugin($plugin, now())
        ->getSubscription();
})->throws(SubscriptionRequiredExp::class);

it('can add plugin to the current subscription', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();
    $plugin = Product::factory()->create();

    $subscription = LaSubscription::make($subscriber)
        ->subscribeTo($plan, now(), 12)
        ->addPlugin($plugin)
        ->getSubscription();

    expect($subscription->isValid())->toBeTrue();
    expect($subscription->contracts)->toHaveCount(1);
    expect($subscription->contracts->pluck('code'))->toContain($plugin->code);
    expect($subscriber->hasPlugin($plugin->code))->toBeTrue();
});

it('can cancel plugin in the current subscription', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();
    $plugin = Product::factory()->create();

    $subscription = LaSubscription::make($subscriber)
        ->subscribeTo($plan, now(), 12)
        ->addPlugin($plugin)
        ->cancelPlugin($plugin)
        ->getSubscription();

    expect($subscription->isValid())->toBeTrue();
    expect($subscription->contracts)->toHaveCount(1);
    expect($subscription->contracts->pluck('code'))->toContain($plugin->code);
    expect($subscriber->getPlugin($plugin->code)->isActive())->toBeFalse();
});

it('can resume plugin in the current subscription after cancellation', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();
    $plugin = Product::factory()->create();

    $manager = LaSubscription::make($subscriber)
        ->subscribeTo($plan, now(), 12)
        ->addPlugin($plugin)
        ->cancelPlugin($plugin);
    expect($subscriber->getPlugin($plugin->code)->isActive())->toBeFalse();
    $manager->resumePlugin($plugin);
    expect($subscriber->getPlugin($plugin->code)->isActive())->toBeTrue();
});
