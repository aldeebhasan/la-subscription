<?php

namespace Aldeebhasan\LaSubscription\Concerns;

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface SubscriberUI
{
    public function getSubscription(): ?Subscription;

    public function subscription(): BelongsTo;
}
