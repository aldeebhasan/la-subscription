<?php

namespace Aldeebhasan\LaSubscription\Http\Resources;

use Aldeebhasan\LaSubscription\Models\ContractTransaction;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Aldeebhasan\LaSubscription\Models\SubscriptionContract;
use Aldeebhasan\NaiveCrud\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class SubscriptionResource extends BaseResource
{
    /** @return array<string,mixed> */
    public function toIndexArray(Request $request): array
    {
        /* @var Subscription $this */
        return [
            'id' => $this->id,
            'plan' => $this->plan->name ?? "-",
            'start_at' => $this->start_at->toDateTimeString(),
            'end_at' => $this->end_at->toDateTimeString(),
            /* @phpstan-ignore-next-line */
            'status' => $this->getStatus(),
            'created_at' => carbonParse($this->created_at)->toDateTimeString(),
        ];
    }

    /** @return array<string,mixed> */
    public function toShowArray(Request $request): array
    {
        /* @var Subscription $this */
        return [
            'id' => $this->id,
            'plan' => $this->plan->name ?? "-",
            'subscriber' => str(class_basename($this->subscriber))->singular() . "({$this->subscriber_id})",
            'start_at' => $this->start_at->toDateTimeString(),
            'end_at' => $this->end_at->toDateTimeString(),
            'suppressed_at' => $this->suppressed_at?->toDateTimeString() ?? "-",
            'canceled_at' => $this->canceled_at?->toDateTimeString() ?? "-",
            /* @phpstan-ignore-next-line */
            'status' => $this->getStatus(),
            'unlimited' => (bool)$this->unlimited,
            'created_at' => carbonParse($this->created_at)->toDateTimeString(),
            'contracts' => $this->contracts->map(fn(SubscriptionContract $contract, int $_) => [
                'number' => $contract->number,
                'name' => $contract->product->name ?? "-",
                'start_at' => $contract->start_at->toDateTimeString(),
                'end_at' => $contract->end_at?->toDateTimeString() ?? "-",
                'auto_renew' => (bool)$contract->auto_renew,
                'transactions' => $contract->transactions->map(fn(ContractTransaction $transaction, int $_) => [
                    'type' => $transaction->type,
                    'start_at' => $transaction->start_at->toDateTimeString(),
                    'end_at' => $transaction->end_at?->toDateTimeString() ?? "-",
                    'causative' => str(class_basename($transaction->causative))->singular() . "({$transaction->causative_id})",
                ]),
            ]),
        ];
    }
}
