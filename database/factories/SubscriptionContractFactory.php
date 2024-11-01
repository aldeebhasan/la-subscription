<?php

namespace Aldeebhasan\LaSubscription\Database\Factories;

use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionContractFactory extends Factory
{
    protected $model = SubscriptionContract::class;

    public function definition(): array
    {
        $product = Product::factory()->create();

        return [
            'billing_period' => $this->faker->numberBetween(1, 24),
            'subscription_id' => Subscription::factory(),
            'code' => $product->code,
            'number' => 1,
            'product_type' => get_class($product),
            'product_id' => $product->getKey(),
            'start_at' => now(),
            'end_at' => now()->addWeek(),
            'type' => BillingCycleEnum::RECURRING,
        ];
    }

    public function forProduct(Model $item): self
    {
        return $this->state([
            'product_type' => get_class($item),
            'product_id' => $item->getKey(),
        ]);
    }
}
