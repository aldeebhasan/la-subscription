<?php

namespace Aldeebhasan\LaSubscription\Database\Factories;

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'start_at' => now(),
            'end_at' => now()->addWeek(),
            'billing_period' => $this->faker->numberBetween(1, 24),
        ];
    }
}
