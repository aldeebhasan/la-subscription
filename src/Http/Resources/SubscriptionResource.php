<?php

namespace Aldeebhasan\LaSubscription\Http\Resources;

use Aldeebhasan\LaSubscription\Models\Subscription;
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
            'suppressed_at' => $this->suppressed_at?->toDateTimeString() ?? "-",
            'canceled_at' => $this->canceled_at?->toDateTimeString() ?? "-",
            'unlimited' => (bool)$this->unlimited,
            'created_at' => carbonParse($this->created_at)->toDateTimeString(),
        ];
    }
}
