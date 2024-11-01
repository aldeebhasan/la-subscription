<?php

namespace Aldeebhasan\LaSubscription\Database\Factories;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'type' => GroupTypeEnum::PLAN,
        ];
    }
}
