<?php

namespace Aldeebhasan\LaSubscription;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Concerns\SubscriberUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Aldeebhasan\LaSubscription\Exceptions\SubscriptionRequiredExp;
use Aldeebhasan\LaSubscription\Exceptions\SwitchToSamePlanExp;
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
        $this->subscription = $builder->setPlan($item)
            ->setStartDate($startAt)
            ->setPeriod($period)
            ->create();

        $this->install($item, $causative, $startAt, $period);

        return $this->subscription;
    }

    /**
     * @throws \Throwable
     */
    public function switchTo(
        ContractUI $item,
        Model $causative,
        ?string $startAt = null,
        ?int $period = null
    ): Subscription {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);
        throw_if($this->subscription->plan_id === $item->getKey(), SwitchToSamePlanExp::class);

        $oldSubscription = $this->subscription;
        $builder = new LaSubscriptionBuilder($this);
        $this->subscription = $builder->setPlan($item)
            ->setStartDate($startAt ?? $this->subscription->start_at)
            ->setPeriod($period ?? $this->subscription->billing_period)
            ->create();

        $oldSubscription->suppress();
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

        $contract = $this->getContract($item, $startAt, $period);
        $transaction = $this->logTransaction($contract, $causative, $startAt, $period);

        if ($contract->end_at && Carbon::parse($contract->end_at)->lt($transaction->end_at)) {
            $contract->update(['end_at' => $transaction->end_at]);
        }
        if ($this->subscription->end_at && Carbon::parse($this->subscription->end_at)->lt($transaction->end_at)) {
            $this->subscription->update(['end_at' => $transaction->end_at]);
        }

        $this->sync();

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function cancel(
        ContractUI $item,
        Model $causative,
        bool $force = false
    ): self {
        throw_if(!$this->subscription, SubscriptionRequiredExp::class);

        $contract = $this->getContract($item);
        $this->logTransaction($contract, $causative, now()->toDateTimeString(), 0, TransactionTypeEnum::CANCEL);
        if ($force) {
            $contract->update(['end_at' => now()->toDateTimeString()]);
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
        ?TransactionTypeEnum $type = null,
    ): SubscriptionContractTransaction {
        [$startAt, $endAt] = $this->getDateRange($startAt, $period);

        /* @var SubscriptionContractTransaction */
        return $contract->transactions()->create([
            'type' => $type ?: ($contract->transactions()->exists() ? TransactionTypeEnum::RENEW : TransactionTypeEnum::NEW),
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
        $this->subscription->quotas()->forceDelete();

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
        $old = $this->subscription->quotas()->where('code', $feature->code)->first();
        if (!$old) {
            $consumed = $feature->isConsumable()
                ? $this->subscription->consumptions()->where('feature_id', $feature->getKey())
                    ->whereDate("created_at", '<=', $this->subscription->end_at)
                    ->whereDate("created_at", '>=', Carbon::parse($this->subscription->end_at)->subMonths($this->subscription->billing_period)->toDateString())
                    ->sum('consumed')
                : 0;

            $this->subscription->quotas()->create([
                'code' => $feature->code,
                'limited' => $feature->isConsumable(),
                'feature_id' => $feature->getKey(),
                'quota' => $feature->isConsumable() ? $quota : 0,
                'consumed' => $consumed ?: 0,
                'end_at' => $this->subscription->end_at,
            ]);
        } else {
            $old->update([
                'quota' => $feature->isConsumable() ? ($old->quota + $quota) : 0,
            ]);
        }
    }
}
