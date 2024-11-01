<?php

namespace Aldeebhasan\LaSubscription\Observers;

use Aldeebhasan\LaSubscription\Models\SubscriptionContract;

class SubscriptionContractObserver
{
    public function creating(SubscriptionContract $item): void
    {
        $nextNumber = SubscriptionContract::latest()->first()->number ?? 0;
        $item->setAttribute('number', $nextNumber + 1);
    }
}
