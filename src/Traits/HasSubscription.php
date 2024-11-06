<?php

namespace Aldeebhasan\LaSubscription\Traits;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Enums\ConsumptionTypeEnum;
use Aldeebhasan\LaSubscription\Exceptions\FeatureNotFoundExp;
use Aldeebhasan\LaSubscription\Exceptions\FeatureQuotaLimitExp;
use Aldeebhasan\LaSubscription\LaSubscription;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionQuota;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/** @property Subscription|null $lastSubscription */
trait HasSubscription
{
    protected ?Collection $loadedSubscriptionQuotas = null;
    protected ?Subscription $subscription = null;

    public function getSubscription(bool $fresh = false): ?Subscription
    {
        if (!is_null($this->subscription) && !$fresh) {
            return $this->subscription;
        }

        if (!$this->relationLoaded('lastSubscription') || $fresh) {
            $this->load('lastSubscription');
        }

        return $this->subscription = $this->lastSubscription;
    }

    /** @return  Collection<SubscriptionQuota> */
    protected function getSubscriptionQuotas(): Collection
    {
        if (!is_null($this->loadedSubscriptionQuotas)) {
            return $this->loadedSubscriptionQuotas;
        }

        return $this->loadedSubscriptionQuotas = $this->getSubscription()?->quotas ?? collect();
    }

    protected function getFeature(string $code): ?SubscriptionQuota
    {
        return $this->getSubscriptionQuotas()->firstWhere('code', $code);
    }

    public function hasFeature($featureName): bool
    {
        return empty($this->getFeature($featureName));
    }

    public function lastSubscription(): MorphOne
    {
        return $this->morphOne(Subscription::class, 'subscriber')->ofMany("start_at", 'MAX');
    }

    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscriber');
    }

    public function isSubscribedTo(ContractUI $plan): bool
    {
        return $this->getSubscription()->plan_id === $plan->getKey();
    }

    public function subscriptionHandler(): LaSubscription
    {
        return LaSubscription::make($this);
    }

    public function canConsume(string|array $codes): bool
    {
        foreach (Arr::wrap($codes) as $code) {
            $quota = $this->getFeature($code);
            if (!$quota || !$quota->canUse()) {
                return false;
            }
        }

        return true;
    }

    public function canConsumeAny(string|array $codes): bool
    {
        $canUse = false;
        foreach (Arr::wrap($codes) as $code) {
            $quota = $this->getFeature($code);
            $canUse = $canUse || ($quota && $quota->canUse());
        }

        return $canUse;
    }

    /**
     * @throws \Throwable
     */
    public function consume(string $code, float $amount = 1): void
    {
        $quota = $this->getFeature($code);

        throw_if(!$quota, FeatureNotFoundExp::class);
        throw_if(!$quota->canUse(), FeatureQuotaLimitExp::class);

        if ($quota->limited) {
            $quota->increment('consumed', $amount);
            $this->getSubscription()->consumptions()->create([
                'code' => $quota->code,
                'feature_id' => $quota->feature_id,
                'consumed' => $amount,
                'type' => ConsumptionTypeEnum::INC,
            ]);
        }
    }

    /**
     * @throws \Throwable
     */
    public function unConsume(string $code, float $amount = 1): void
    {
        $quota = $this->getFeature($code);

        throw_if(!$quota, FeatureNotFoundExp::class);

        if ($quota->limited) {
            $quota->decrement('consumed', $amount);
            $this->getSubscription()->consumptions()->create([
                'code' => $quota->code,
                'feature_id' => $quota->feature_id,
                'consumed' => $amount,
                'type' => ConsumptionTypeEnum::DEC,
            ]);
        }
    }

    /**
     * @throws \Throwable
     */
    public function getCurrentConsumption(string $code): int|float
    {
        $quota = $this->getFeature($code);

        throw_if(!$quota, FeatureNotFoundExp::class);

        if ($quota->limited) {
            return max($quota->consumed, 0);
        }

        return 0;
    }

    /**
     * @throws \Throwable
     */
    public function getBalance(string $code): int|float
    {
        $quota = $this->getFeature($code);

        throw_if(!$quota, FeatureNotFoundExp::class);

        if ($quota->limited) {
            return max($quota->quota - $quota->consumed, 0);
        }

        return 0;
    }
}
