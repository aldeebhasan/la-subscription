<?php

namespace Aldeebhasan\LaSubscription\Handler;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Aldeebhasan\LaSubscription\Models\SubscriptionContractTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

readonly class ContractsHandler
{

    public function __construct(private Subscription $subscription)
    {
    }


    /**
     * @return Collection<SubscriptionContract>
     */
    public function getActivePlugin(): Collection
    {
        return $this->subscription->contracts()->active()->valid()->get();
    }

    public function install(
        ContractUI $item,
        Model $causative,
        ?string $startAt = null,
        ?int $period = null
    ): self {

        $contract = $this->getContract($item, $startAt, $period);
        $transaction = $this->logTransaction($contract, $causative, $startAt, $period);

        if ($contract->end_at && $contract->end_at->lt($transaction->end_at)) {
            $contract->update(['end_at' => $transaction->end_at, 'auto_renew' => true]);
        }
        if ($this->subscription->end_at && $this->subscription->end_at->lt($transaction->end_at)) {
            $this->subscription->update(['end_at' => $transaction->end_at]);
        }

        return $this;
    }


    public function cancel(ContractUI $item, Model $causative): self
    {
        $contract = $this->getContract($item);
        $this->logTransaction($contract, $causative, type: TransactionTypeEnum::CANCEL);

        $contract->update(['auto_renew' => false]);

        return $this;
    }

    public function resume(ContractUI $item, Model $causative): self
    {

        $contract = $this->getContract($item);
        $this->logTransaction($contract, $causative, type: TransactionTypeEnum::RESUME);

        $contract->update(['auto_renew' => true]);

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
            : $this->subscription->end_at->toDateTimeString();

        return [$startAt, $endAt];
    }

    public function sync(): void
    {
        $this->subscription->quotas()->forceDelete();

        $this->subscription->plan->getFeatures()->each(function (Feature $feature, int|string $_) {
            if ($feature->pivot->active ?? false) {
                $this->syncFeature($feature, $feature->pivot->value);
            }
        });

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
                    ->whereDate("created_at", '>=', $this->subscription->end_at->subMonths($this->subscription->getBillingPeriod())->toDateString())
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
