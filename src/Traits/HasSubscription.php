<?php

namespace Aldeebhasan\LaSubscription\Traits;

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasSubscription
{
    public function getSubscription(): ?Subscription
    {
        /* @var Subscription|null */
        return $this->subscription()->first();
    }

    public function subscription(): BelongsTo
    {
        /* @var  Model $this */
        return $this->morphOne(Subscription::class, 'owner');
    }
}
