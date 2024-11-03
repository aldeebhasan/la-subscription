<?php

namespace Aldeebhasan\LaSubscription\Traits;

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/** @property Subscription|null $subscription */
trait HasSubscription
{
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function subscription(): MorphOne
    {
        /* @var  Model $this */
        return $this->morphOne(Subscription::class, 'subscriber')->ofMany("start_at", 'MAX');
    }

    public function subscriptions(): MorphMany
    {
        /* @var  Model $this */
        return $this->morphMany(Subscription::class, 'subscriber');
    }

    //    public function can(string|array $code): bool
    //    {
    //        $subscription = $this->getSubscription();
    //        $subscription->loadMissing(['contracts' => fn($q) => $q->valid(), 'contracts.product']);
    //
    //
    //    }
}
