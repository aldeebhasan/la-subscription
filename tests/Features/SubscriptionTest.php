<?php


use Aldeebhasan\LaSubscription\LaSubscription;
use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Tests\Sample\App\Models\User;

pest()->group('subscription');


it('can subscribe to plan', function () {

    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();

    LaSubscription::make($subscriber)->subscribeTo($plan, now()->toDateTimeString(), 12);

    $subscription = Subscription::first();
    expect($subscription)->not->toBeNull();
    expect($subscription->isOnGracePeriod())->toBeTrue();
    expect($subscription->end_at->toDateString())->toBe(now()->addMonths(12)->toDateString());
});

it('can subscribe to plan in future', function () {

    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();

    LaSubscription::make($subscriber)->subscribeTo($plan, now()->addMonth()->toDateTimeString(), 12);

    $subscription = Subscription::first();
    expect($subscription)->not->toBeNull();
    expect($subscription->isOnGracePeriod())->toBeFalse();
    expect($subscription->end_at->toDateString())->toBe(now()->addMonth()->addMonths(12)->toDateString());
});
