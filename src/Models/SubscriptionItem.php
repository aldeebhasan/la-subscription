<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionItem extends Model
{
    protected $fillable = ['subscription_id', 'code', 'number', 'product_type', 'product_id', 'start_at', 'end_at', 'type'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'type' => BillingCycleEnum::class,
    ];

    public function getTable(): string
    {
        $prefix = config('subscription.prefix');
        $table = parent::getTable();

        return "{$prefix}_{$table}";
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(SubscriptionItemTransaction::class);
    }
}
