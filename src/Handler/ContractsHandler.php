<?php

namespace Aldeebhasan\LaSubscription\Handler;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Aldeebhasan\LaSubscription\Models\SubscriptionContractTransaction;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        string|CarbonInterface|null $startAt = null,
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
        string|CarbonInterface|null $startAt = null,
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
        string|CarbonInterface|null $startAt = null,
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
    private function getDateRange(string|CarbonInterface|null $startAt = null, ?int $period = null): array
    {
        $startAt = $startAt ? carbonParse($startAt) : now();
        $endAt = $period
            ? $startAt->clone()->addMonths($period)
            : $this->subscription->end_at;

        return [$startAt->toDateTimeString(), $endAt->toDateTimeString()];
    }

    public function sync(): void
    {
        $this->subscription->quotas()->forceDelete();

        $this->subscription->plan->getFeatures()->each(function (Feature $feature, int|string $_) {
            if ($feature->pivot->active ?? false) {
                $this->syncFeature($feature, $feature->pivot->value, false);
            }
        });

        $this->subscription->contracts()->with('product')->valid()
            ->get()->each(function (SubscriptionContract $contract) {
                $contract->product->getFeatures()->each(function (Feature $feature, int|string $_) use ($contract) {
                    if ($feature->pivot->active ?? false) {
                        $this->syncFeature($feature, $feature->pivot->value, $contract->product->isRecurring());
                    }
                });
            });
    }

    private function syncFeature(Feature $feature, ?int $quota = null, bool $isRecurring = false): void
    {
        $old = $this->subscription->quotas()->where('code', $feature->code)->first();
        if (!$old) {
            $consumed = $feature->isConsumable()
                ? $this->subscription->consumptions()->where('feature_id', $feature->getKey())
                    ->valid($this->subscription->end_at->subMonths($this->subscription->getBillingPeriod()), $this->subscription->end_at)
                    ->sum(DB::raw("IF(type = 'increase',consumed,-consumed)"))
                : 0;
            $quota = $quota ?: 0;
            $this->subscription->quotas()->create([
                'code' => $feature->code,
                'limited' => $feature->isConsumable(),
                'feature_id' => $feature->getKey(),
                'quota' => $feature->isConsumable() ? $quota : 0,
                'consumed' => $consumed,
                'end_at' => $isRecurring ? $this->subscription->end_at : null,
            ]);
        } else {
            $old->update([
                'quota' => $feature->isConsumable() ? ($old->quota + $quota) : 0,
            ]);
        }
    }
}
