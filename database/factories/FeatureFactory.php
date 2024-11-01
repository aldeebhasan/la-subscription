<?php

namespace Aldeebhasan\LaSubscription\Database\Factories;

use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition(): array
    {
        return [
            'name' => $name = $this->faker->name,
            'code' => str($name)->slug(),
            'description' => $this->faker->text,
            'group_id' => Group::factory(),
            'active' => true,
            'limited' => true
        ];
    }
}
