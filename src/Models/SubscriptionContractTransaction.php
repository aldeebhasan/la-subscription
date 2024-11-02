<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $start_at
 * @property string $end_at
 */
class SubscriptionContractTransaction extends LaModel
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
