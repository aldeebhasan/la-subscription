<?php

use Aldeebhasan\LaSubscription\Exceptions\SwitchToSamePlanExp;
use Aldeebhasan\LaSubscription\LaSubscription;
use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Tests\Sample\App\Models\User;

if (version_compare(\Pest\version(), "3.0.0") >= 0) {
    pest()->group('subscription');
}

it('can subscribe to plan', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();

    $subscription = LaSubscription::make($subscriber)
        ->subscribeTo($plan, now(), 12)
        ->getSubscription();

    expect($subscription)->not->toBeNull();
    expect($subscription->isOnGracePeriod())->toBeTrue();
    expect($subscription->end_at->toDateString())->toBe(now()->addMonths(12)->toDateString());
});

it('can subscribe to plan in future', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();

    $subscription = LaSubscription::make($subscriber)
        ->subscribeTo($plan, now()->addMonth(), 12)
        ->getSubscription();

    $subscription = $subscriber->getSubscription();
    expect($subscription)->not->toBeNull();
    expect($subscription->isOnGracePeriod())->toBeFalse();
    expect($subscription->end_at->toDateString())->toBe(now()->addMonths(13)->toDateString());
});

it('cann\'t switch to same plan', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now(), 12)
        ->getSubscription();

    LaSubscription::make($subscriber)
        ->switchTo($plan, now(), 12)
        ->getSubscription();
})->throws(SwitchToSamePlanExp::class);

it('can switch to new plan', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();

    $oldSubscription = LaSubscription::make($subscriber)
        ->subscribeTo($plan, now(), 12)
        ->getSubscription();

    $secondPlan = Product::factory()->create();
    $newSubscription = LaSubscription::make($subscriber)
        ->switchTo($secondPlan, now(), 12)
        ->getSubscription();
    $oldSubscription->refresh();

    expect(Subscription::count())->toBe(2);
    expect($oldSubscription->isActive())->toBeFalse();
    expect($newSubscription->isActive())->toBeTrue();
    expect($newSubscription->isOnGracePeriod())->toBeTrue();
    expect($newSubscription->end_at->toDateString())->toBe(now()->addMonths(12)->toDateString());
});

it('can switch to new plan in future', function () {
    $plan = Product::factory()->create();
    $subscriber = User::factory()->create();

    $oldSubscription = LaSubscription::make($subscriber)
        ->subscribeTo($plan, now(), 12)
        ->getSubscription();

    $secondPlan = Product::factory()->create();
    $newSubscription = LaSubscription::make($subscriber)
        ->switchTo($secondPlan, now()->addMonth(), 12)
        ->getSubscription();
    $oldSubscription->refresh();

    expect(Subscription::count())->toBe(2);
    expect($oldSubscription->isActive())->toBeFalse();
    expect($newSubscription->isActive())->toBeFalse();
    expect($newSubscription->isOnGracePeriod())->toBeFalse();
    expect($newSubscription->end_at->toDateString())->toBe(now()->addMonths(13)->toDateString());
});
