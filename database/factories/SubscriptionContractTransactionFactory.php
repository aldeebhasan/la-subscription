<?php

namespace Aldeebhasan\LaSubscription\Database\Factories;

use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Aldeebhasan\LaSubscription\Models\SubscriptionContractTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionContractTransactionFactory extends Factory
{
    protected $model = SubscriptionContractTransaction::class;

    public function definition(): array
    {
        return [
            'subscription_item_id'=>SubscriptionContract::factory(),
            'start_at' => now(),
            'end_at'=>now()->addMonths(),
        ];
    }

    public function causedBy(Model $item): self
    {
        return $this->state([
            'causative_type' => get_class($item),
            'causative_id' => $item->getKey(),
        ]);
    }
}
