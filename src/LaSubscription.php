<?php

namespace Aldeebhasan\LaSubscription;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Concerns\SubscriberUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Aldeebhasan\LaSubscription\Exceptions\SubscriptionRequiredExp;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Aldeebhasan\LaSubscription\Models\SubscriptionContractTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LaSubscription
{
    private ?Subscription $subscription;

    public static function make(SubscriberUI $subscriber): self
    {
        return new self($subscriber);
    }

    private function __construct(private readonly SubscriberUI $subscriber)
    {
        $this->subscription = $subscriber->getSubscription();
    }

    public function refreshSubscription(): self
    {
        $this->subscription = $this->subscriber->getSubscription();

        return $this;
    }

    public function getSubscriber(): SubscriberUI
    {
        return $this->subscriber;
    }

    /**
     * @throws \Throwable
     */
    public function subscribeTo(
        ContractUI $item,
        Model $causative,
        ?string $startAt = null,
        int $period = 1
    ): Subscription {
        $builder = new LaSubscriptionBuilder($this);
        $this->subscription = $builder->setStartDate($startAt)
            ->setPeriod($period)
            ->create();

        $this->install($item, $causative, $startAt, $period);

        return $this->subscription;
    }

    /**
     * @throws \Throwable
     */
    public function install(
        ContractUI $item,
        Model $causative,
        ?string $startAt = null,
        ?int $period = null
    ): self {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $item = $this->getContract($item, $startAt, $period);
        $transaction = $this->logTransaction($item, $causative, $startAt, $period);

        if ($item->end_at && Carbon::parse($item->end_at)->lt($transaction->end_at)) {
            $item->update(['end_at' => $transaction->end_at]);
        }
        if ($this->subscription->end_at && Carbon::parse($this->subscription->end_at)->lt($transaction->end_at)) {
            $this->subscription->update(['end_at' => $transaction->end_at]);
        }

        $this->sync();

        return $this;
    }

    private function getContract(
        ContractUI $item,
        ?string $startAt = null,
        ?int $period = null
    ): SubscriptionContract {
        $contractItem = $this->subscription->contracts()->firstWhere('code', $item->getCode());

        if (!$contractItem) {
            [$startAt, $endAt] = $this->getDateRange($startAt, $period);
            $contractItem = $this->subscription->contracts()->create([
                'code' => $item->getCode(),
                'product_type' => get_class($item),
                'product_id' => $item->getKey(),
                'start_at' => $startAt,
                'end_at' => $item->isRecurring() ? $endAt : null,
                'type' => $item->isRecurring() ? BillingCycleEnum::RECURRING : BillingCycleEnum::NON_RECURRING,
            ]);
        }

        return $contractItem;
    }

    private function logTransaction(
        SubscriptionContract $contract,
        Model $causative,
        ?string $startAt = null,
        ?int $period = null,
    ): SubscriptionContractTransaction {
        [$startAt, $endAt] = $this->getDateRange($startAt, $period);

        /* @var SubscriptionContractTransaction */
        return $contract->transactions()->create([
            'type' => $contract->transactions()->exists() ? TransactionTypeEnum::RENEW : TransactionTypeEnum::NEW,
            'start_at' => $startAt,
            'end_at' => $contract->type === BillingCycleEnum::NON_RECURRING ? null : $endAt,
            'causative_type' => get_class($causative),
            'causative_id' => $causative->getKey(),
        ]);
    }

    /**
     * @return array<string>
     */
    private function getDateRange(?string $startAt = null, ?int $period = null): array
    {
        $startAt ??= now()->toDateTimeString();
        $endAt = $period
            ? Carbon::parse($startAt)->addMonths($period)->toDateTimeString()
            : $this->subscription->end_at;

        return [$startAt, $endAt];
    }

    private function sync(): void
    {
        $this->subscription->consumptions()->forceDelete();

        $this->subscription->contracts()->valid()
            ->get()->each(function (SubscriptionContract $contract) {
                $contract->product->getFeatures()->each(function (Feature $feature, int|string $_) {
                    if ($feature->pivot->active ?? false) {
                        $this->syncFeature($feature, $feature->pivot->value);
                    }
                });
            });
    }

    private function syncFeature(Feature $feature, ?int $quota = null): void
    {
        $old = $this->subscription->consumptions()->where('code', $feature->code)->first();
        if (!$old) {
            $this->subscription->consumptions()->create([
                'code' => $feature->code,
                'limited' => $feature->limited,
                'feature_id' => $feature->getKey(),
                'quota' => $feature->limited ? $quota : 0,
                'end_at' => $this->subscription->end_at,
            ]);
        } else {
            $old->update([
                'quota' => $feature->limited ? ($old->quota + $quota) : 0,
            ]);
        }
    }
}
