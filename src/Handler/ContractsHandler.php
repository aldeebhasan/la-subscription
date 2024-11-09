<?php

namespace Aldeebhasan\LaSubscription\Handler;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Aldeebhasan\LaSubscription\Models\ContractTransaction;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Aldeebhasan\LaSubscription\Models\SubscriptionQuota;
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
    ): ContractTransaction {
        [$startAt, $endAt] = $this->getDateRange($startAt, $period);

        /* @var ContractTransaction */
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
        $quotas = [];
        $this->subscription->plan->getFeatures()->each(function (Feature $feature, int|string $_) use (&$quotas) {
            if ($feature->pivot->active ?? false) {
                $quotas[$feature->code] = [
                    'feature' => $feature,
                    'quota' => $feature->isConsumable() ? (float)$feature->pivot->value : 0,
                    'end_at' => $this->subscription->end_at,
                ];
            }
        });
        $this->subscription->contracts()->with('product')->valid()
            ->get()->each(function (SubscriptionContract $contract) use (&$quotas) {
                $features = $contract->product->getFeatures();
                /** @var Feature $feature */
                foreach ($features as $feature) {
                    if ($feature->pivot->active ?? false) {
                        $oldQuota = (float)($quotas[$feature->code]['quota'] ?? 0);
                        $quotas[$feature->code] = [
                            'feature' => $feature,
                            'quota' => $oldQuota + ($feature->isConsumable() ? (float)$feature->pivot->value : 0),
                            'end_at' => $quotas[$feature->code]['end_at'] ?? ($contract->product->isRecurring() ? $this->subscription->end_at : null),
                        ];
                    }
                }
            });

        $ids = [];
        foreach ($quotas as $quota) {
            $quota = $this->syncFeature($quota['feature'], $quota['quota'], $quota['end_at']);
            $ids[] = $quota->getKey();
        }

        $this->subscription->quotas()->whereKeyNot($ids)->delete();
    }

    private function syncFeature(Feature $feature, int $quota, string|CarbonInterface|null $endAt): SubscriptionQuota
    {
        $consumed = $feature->isConsumable()
            ? $this->subscription->consumptions()->where('feature_id', $feature->getKey())
                ->valid($this->subscription->end_at->subMonths($this->subscription->getBillingPeriod()), $this->subscription->end_at)
                ->sum(DB::raw("CASE WHEN type = 'increase' THEN consumed ELSE -consumed END"))
            : 0;

        return $this->subscription->quotas()->updateOrCreate([
            'code' => $feature->code,
        ], [
            'limited' => $feature->isConsumable(),
            'feature_id' => $feature->getKey(),
            'quota' => $feature->isConsumable() ? $quota : 0,
            'consumed' => $consumed ?: 0,
            'end_at' => $endAt,
        ]);
    }
}
