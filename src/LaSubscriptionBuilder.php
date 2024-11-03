<?php

namespace Aldeebhasan\LaSubscription;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Support\Carbon;

class LaSubscriptionBuilder
{
    private ContractUI $plan;
    private string $startDate;
    private string $endDate;
    private int $period = 1;

    public function __construct(private readonly LaSubscription $manager)
    {
    }

    public function setPlan(ContractUI $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function setStartDate(string $date): self
    {
        $this->startDate = $date;

        return $this;
    }

    public function getStartDate(): string
    {
        return $this->startDate ?? now()->toDateTimeString();
    }

    public function setEndDate(string $date): self
    {
        $this->endDate = $date;

        return $this;
    }

    public function getEndDate(): string
    {
        return $this->endDate ?: Carbon::parse($this->getStartDate())->addMonths($this->period)->toDateTimeString();
    }

    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getPeriod(): int
    {
        return $this->endDate
            ? ceil(Carbon::parse($this->getStartDate())->diffInMonths($this->getStartDate()))
            : $this->period;
    }

    public function create(): Subscription
    {
        $owner = $this->manager->getSubscriber();

        $owner->subscriptions()->create([
            'plan_id' => $this->plan->getKey(),
            'start_at' => $this->getStartDate(),
            'end_at' => $this->getEndDate(),
            'billing_period' => $this->getPeriod(),
        ]);

        return $owner->getSubscription();
    }
}
