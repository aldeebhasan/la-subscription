<?php

namespace Aldeebhasan\LaSubscription;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Concerns\SubscriberUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class LaSubscription
{
    private ?Subscription $subscription;

    public static function make(SubscriberUI $owner): self
    {
        return new self($owner);
    }

    private function __construct(private readonly SubscriberUI $owner)
    {
        $this->subscription = $owner->getSubscription();
    }

    public function refreshSubscription(): self
    {
        $this->subscription = $this->owner->getSubscription();

        return $this;
    }

    public function getOwner(): SubscriberUI
    {
        return $this->owner;
    }

    public function subscribeTo(
        ContractUI $item,
        Model $causative,
        ?string $startAt = null,
        ?int $period = null
    ): Subscription {
        $builder = new LaSubscriptionBuilder($this);
        $this->subscription = $builder->setStartDate($startAt)
            ->setPeriod($period ?? 1)
            ->create();

        $this->install($item, $causative, $startAt, $period);

        return $this->subscription;
    }

    public function install(
        ContractUI $item,
        Model $causative,
        ?string $startAt = null,
        ?int $period = null
    ): self {
        $item = $this->getContract($item, $startAt, $period);
        $this->logTransaction($item, $causative, $startAt, $period);

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
                'product_id' => $item->getId(),
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
    ): void {
        [$startAt, $endAt] = $this->getDateRange($startAt, $period);
        $contract->transactions()->create([
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
}
