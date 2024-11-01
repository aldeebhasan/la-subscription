<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Observers\SubscriptionContractObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(SubscriptionContractObserver::class)]
class SubscriptionContract extends LaModel
{
    protected $fillable = ['subscription_id', 'code', 'number', 'product_type', 'product_id', 'start_at', 'end_at', 'type'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'type' => BillingCycleEnum::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SubscriptionContractTransaction::class);
    }
}
