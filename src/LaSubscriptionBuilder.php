<?php

namespace Aldeebhasan\LaSubscription;

use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Support\Carbon;

class LaSubscriptionBuilder
{
    private string $startDate;
    private string $endDate;
    private int $period = 1;

    public function __construct(private readonly LaSubscription $manager)
    {
    }

    public function setStartDate(string $date): self
    {
        $this->startDate = $date;

        return $this;
    }

    public function getStartDate(): string
    {
        return $this->startDate ?? now()->toDayDateTimeString();
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

        $subscription = $owner->getSubscription();
        if (!$subscription) {
            $owner->subscription()->create([
                'start_at' => $this->getStartDate(),
                'end_at' => $this->getEndDate(),
                'billing_period' => $this->getPeriod(),
            ]);
        } else {
            $this->update($subscription);
        }

        return $owner->getSubscription();
    }

    private function update(Subscription $subscription): void
    {
        $subscription->update([
            //            'start_at' => $this->getStartDate(),
            'end_at' => $this->getEndDate(),
            'billing_period' => $this->getPeriod(),
        ]);
    }
}
