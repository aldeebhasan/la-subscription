<?php

namespace Aldeebhasan\LaSubscription\Traits;

use Aldeebhasan\LaSubscription\Concerns\SubscriberUI;
use Aldeebhasan\LaSubscription\LaSubscription;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionQuota;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/** @property Subscription|null $subscription */
trait HasSubscription
{
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /** @return  Collection<SubscriptionQuota> */
    public function getSubscriptionQuotas(): Collection
    {
        return $this->getSubscription()?->quotas ?? collect();
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

    public function subscriptionHandler(): LaSubscription
    {
        /* @var  SubscriberUI $this */
        return LaSubscription::make($this);
    }

    public function canUse(string|array $codes): bool
    {
        $quotas = $this->getSubscriptionQuotas();

        foreach (Arr::wrap($codes) as $code) {
            /** @var SubscriptionQuota $quota */
            $quota = $quotas->firstWhere('code', $code);
            if (!$quota || !$quota->canUse()) {
                return false;
            }
        }

        return true;
    }

    public function consume(string $code): void
    {
        $quotas = $this->getSubscriptionQuotas();
    }
}
