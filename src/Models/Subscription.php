<?php

namespace Aldeebhasan\LaSubscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Collection<SubscriptionContract> $contracts
 * @property Product $plan
 * @property Model $subscriber
 * @property Collection<SubscriptionQuota> $quotas
 * @property Collection<FeatureConsumption> $consumptions
 * @property Carbon $start_at
 * @property ?Carbon $end_at
 */
class Subscription extends LaModel
{
    protected $fillable = ['subscriber_type', 'subscriber_id', 'plan_id', 'start_at', 'end_at', 'supersede_at', 'canceled_at', 'billing_period'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'supersede_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function subscriber(): MorphTo
    {
        return $this->morphTo();
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(SubscriptionContract::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'plan_id');
    }

    public function quotas(): HasMany
    {
        return $this->hasMany(SubscriptionQuota::class);
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(FeatureConsumption::class);
    }

    public function isCanceled(): bool
    {
        return !is_null($this->canceled_at);
    }

    public function isSupersede(): bool
    {
        return !is_null($this->supersede_at);
    }

    public function getBillingPeriod(): int
    {
        return $this->billing_period ?? 1;
    }
}
