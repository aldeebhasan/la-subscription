<?php

namespace Aldeebhasan\LaSubscription\Handler;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Concerns\SubscriberUI;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Support\Carbon;

class SubscriptionBuilder
{
    private ContractUI $plan;
    private string $startDate;
    private string $endDate;
    private int $period = 1;

    public static function make(SubscriberUI $subscriber): self
    {
        return new self($subscriber);
    }

    private function __construct(private readonly SubscriberUI $subscriber)
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
        $this->subscriber->subscriptions()->create([
            'plan_id' => $this->plan->getKey(),
            'start_at' => $this->getStartDate(),
            'end_at' => $this->getEndDate(),
            'billing_period' => $this->getPeriod(),
        ]);

        return $this->subscriber->getSubscription();
    }
}