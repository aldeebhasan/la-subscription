<?php

namespace Aldeebhasan\LaSubscription\Events;

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(readonly public Subscription $subscription)
    {
    }
}
