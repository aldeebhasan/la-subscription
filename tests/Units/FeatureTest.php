<?php

use Aldeebhasan\LaSubscription\LaSubscription;
use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\LaSubscription\Tests\Sample\App\Models\User;

if (version_compare(\Pest\version(), "3.0.0") >= 0) {
    pest()->group('features');
}

it('can consume unlimited future in subscription', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => false])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->getSubscription();

    expect($subscriber->canConsume($plan->features->first()->code))->toBeTrue();
});

it('can consume unlimited future in addon', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => false])->create();
    $addon = Product::factory()->withFeatures(state: ['limited' => false])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->addPlugin($addon)
        ->getSubscription();

    expect($subscriber->canConsume($addon->features->first()->code))->toBeTrue();
});

it('can consume limited future in subscription', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => true], pivot: ['value' => 1])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->getSubscription();

    expect($subscriber->canConsume($plan->features->first()->code))->toBeTrue();
});

it('can consume limited future in addons', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => false])->create();
    $addon = Product::factory()->withFeatures(state: ['limited' => true], pivot: ['value' => 1])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->addPlugin($addon)
        ->getSubscription();

    expect($subscriber->canConsume($addon->features->first()->code))->toBeTrue();
});

it('can not consume limited future with no quota', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => true], pivot: ['value' => 0])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->getSubscription();

    expect($subscriber->canConsume($plan->features->first()->code))->toBeFalse();
});

it('consume limited future will decrease the quota', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => true], pivot: ['value' => 2])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->getSubscription();

    $subscriber->consume($plan->features->first()->code);
    $featureQuota = $subscriber->getFeature($plan->features->first()->code);
    expect($featureQuota->quota)->toBe((float)2);
    expect($featureQuota->consumed)->toBe((float)1);
    expect($subscriber->getCurrentConsumption($plan->features->first()->code))->toBe((float)1);
});

it('retrieve limited future will increase the quota', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => true], pivot: ['value' => 2])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->getSubscription();

    $subscriber->consume($plan->features->first()->code);
    $subscriber->retrieve($plan->features->first()->code);
    $featureQuota = $subscriber->getFeature($plan->features->first()->code);
    expect($featureQuota->quota)->toBe((float)2);
    expect($featureQuota->consumed)->toBe((float)0);
    expect($subscriber->getCurrentConsumption($plan->features->first()->code))->toBe((float)0);
});

it('consume and retrieve unlimited future ', function () {
    $subscriber = User::factory()->create();
    $plan = Product::factory()->withFeatures(state: ['limited' => false])->create();

    LaSubscription::make($subscriber)
        ->subscribeTo($plan, now())
        ->getSubscription();

    $subscriber->consume($plan->features->first()->code);
    $featureQuota = $subscriber->getFeature($plan->features->first()->code);
    expect($featureQuota->quota)->toBe((float)0);
    expect($featureQuota->consumed)->toBe((float)0);
    expect($subscriber->getCurrentConsumption($plan->features->first()->code))->toBe((float)0);

    $subscriber->retrieve($plan->features->first()->code);
    expect($featureQuota->quota)->toBe((float)0);
    expect($featureQuota->consumed)->toBe((float)0);
    expect($subscriber->getCurrentConsumption($plan->features->first()->code))->toBe((float)0);
});
