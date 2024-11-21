<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Carbon $start_at
 * @property ?Carbon $end_at
 */
class ContractTransaction extends LaModel
{
    protected $fillable = ['subscription_contract_id', 'type', 'start_at', 'end_at', 'causative_type', 'causative_id'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'type' => TransactionTypeEnum::class,
    ];

    public function subscriptionContract(): BelongsTo
    {
        return $this->belongsTo(SubscriptionContract::class);
    }

    public function causative(): MorphTo
    {
        return $this->morphTo();
    }
}
