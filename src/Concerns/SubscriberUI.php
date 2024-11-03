<?php

namespace Aldeebhasan\LaSubscription\Concerns;

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

interface SubscriberUI
{
    public function getSubscription(): ?Subscription;

    public function subscription(): MorphOne;

    public function subscriptions(): MorphMany;
}
