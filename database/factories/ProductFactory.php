<?php

namespace Aldeebhasan\LaSubscription\Database\Factories;

use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Group;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->name,
            'code' => str($name)->slug()->toString(),
            'description' => $this->faker->text,
            'group_id' => Group::factory(),
            'active' => true,
            'type' => BillingCycleEnum::RECURRING,
            'price' => $this->faker->numberBetween(1, 100),
            'price_yearly' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * @param array<string,mixed> $state
     * @param array<string,mixed> $pivot
     */
    public function withFeatures(array $state = [], array $pivot = [], int $count = 1): self
    {
        return $this->hasAttached(Feature::factory($count, $state), $pivot, 'features');
    }
}
