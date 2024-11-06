<?php

namespace Aldeebhasan\LaSubscription;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Events\SubscriptionCanceled;
use Aldeebhasan\LaSubscription\Events\SubscriptionRenewd;
use Aldeebhasan\LaSubscription\Events\SubscriptionStarted;
use Aldeebhasan\LaSubscription\Events\SubscriptionSuppressed;
use Aldeebhasan\LaSubscription\Events\SubscriptionSwitched;
use Aldeebhasan\LaSubscription\Exceptions\SubscriptionRequiredExp;
use Aldeebhasan\LaSubscription\Exceptions\SwitchToSamePlanExp;
use Aldeebhasan\LaSubscription\Handler\ContractsHandler;
use Aldeebhasan\LaSubscription\Handler\SubscriptionBuilder;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Aldeebhasan\LaSubscription\Traits\HasSubscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LaSubscription
{
    private ?Subscription $subscription;
    private ContractsHandler $contractsHandler;

    public static function make(Model $subscriber): self
    {
        return new self($subscriber);
    }

    private function __construct(private readonly Model $subscriber)
    {
        $this->subscription = $this->getSubscription();
        if ($this->subscription) {
            $this->contractsHandler = new ContractsHandler($this->subscription);
        }
    }

    public function reload(): self
    {
        $this->subscription = $this->getSubscription();
        $this->contractsHandler = new ContractsHandler($this->subscription);

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        if (!method_exists($this->subscriber, "getSubscription")) {
            throw new \LogicException("Subscriber should implement HasSubscription Trait");
        }
        return $this->subscriber->getSubscription(true);
    }

    /**
     * @throws \Throwable
     */
    public function subscribeTo(ContractUI $item, ?string $startAt = null, int $period = 1): self
    {
        $this->subscription = SubscriptionBuilder::make($this->subscriber)
            ->setPlan($item)
            ->setStartDate($startAt)
            ->setPeriod($period)
            ->create();

        $this->reload();
        $this->refresh();

        event(new SubscriptionStarted($this->subscription));

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function switchTo(ContractUI $item, ?string $startAt = null, ?int $period = null): self
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);
        throw_if($this->subscription->plan_id === $item->getKey(), SwitchToSamePlanExp::class);

        $this->suppress();
        $this->subscription = SubscriptionBuilder::make($this->subscriber)
            ->setPlan($item)
            ->setStartDate($startAt ?? $this->subscription->start_at->toDateTimeString())
            ->setPeriod($period ?? $this->subscription->getBillingPeriod())
            ->create();

        $this->reload();
        $this->refresh();

        event(new SubscriptionSwitched($this->subscription));

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function cancel(?string $cancelDate = null): self
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $cancelDate = $cancelDate ?: now()->toDateTimeString();
        $this->subscription->update(['canceled_at' => $cancelDate]);

        event(new SubscriptionCanceled($this->subscription));

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function resume(): self
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $this->subscription->update(['canceled_at' => null]);

        event(new SubscriptionCanceled($this->subscription));

        return $this;
    }

    /**
     * @throws \Throwable
     */
    private function suppress(?string $suppressionDate = null): void
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $suppressionDate = $suppressionDate ?: now()->toDateTimeString();
        $this->subscription->update(['suppressed_at' => $suppressionDate]);

        event(new SubscriptionSuppressed($this->subscription));
    }

    /**
     * @throws \Throwable
     */
    public function renew(?int $period = null, bool $withPlugins = true): self
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $period = $period ?: $this->subscription->getBillingPeriod();
        $lastEndDate = $this->subscription->end_at;
        $endDate = Carbon::parse($lastEndDate)->addMonths($period)->endOfDay()->toDateTimeString();
        $this->subscription->update(['end_at' => $endDate]);

        if ($withPlugins) {
            $this->contractsHandler->getActivePlugin()->each(function (SubscriptionContract $contract) use ($lastEndDate) {
                $this->contractsHandler->install($contract->product, $this->subscription->subscriber, $lastEndDate->addDay()->toDateTimeString());
            });
        }

        $this->refresh();

        event(new SubscriptionRenewd($this->subscription));

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function addPlugin(ContractUI $item, ?Model $causative = null, ?string $startAt = null): self
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $this->contractsHandler->install($item, $causative ?? $this->subscription->subscriber, $startAt);

        $this->refresh();

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function cancelPlugin(ContractUI $item, ?Model $causative = null): self
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $this->contractsHandler->cancel($item, $causative ?? $this->subscription->subscriber);

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function resumePlugin(ContractUI $item, ?Model $causative = null): self
    {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $this->contractsHandler->resume($item, $causative ?? $this->subscription->subscriber);

        return $this;
    }

    public function refresh(): self
    {
        $this->contractsHandler->sync();

        return $this;
    }
}
