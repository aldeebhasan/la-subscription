<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Traits\ValidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
    use ValidTrait;

    protected $fillable = ['subscriber_type', 'subscriber_id', 'plan_id', 'start_at', 'end_at', 'suppressed_at', 'canceled_at', 'billing_period', 'unlimited'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'suppressed_at' => 'datetime',
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

    public function isUnlimited(): bool
    {
        return (bool)$this->unlimited;
    }

    public function isCanceled(): bool
    {
        return !is_null($this->canceled_at);
    }

    public function scopeCanceled(Builder $query): Builder
    {
        return $query->whereNotNull('canceled_at');
    }

    public function isSuppressed(): bool
    {
        return !is_null($this->suppressed_at);
    }

    public function scopeSupersede(Builder $query): Builder
    {
        return $query->whereNotNull('suppressed_at');
    }

    public function isActive(): bool
    {
        return $this->isValid()
            && !$this->isCanceled()
            && !$this->isSuppressed();
    }

    public function scopeActive(Builder $query): Builder
    {
        /* @phpstan-ignore-next-line */
        return $query->valid()->whereNot(fn(Builder $query) => $query->suppressed()->orWhere->canceled());
    }

    public function getBillingPeriod(): int
    {
        return $this->billing_period ?? 1;
    }
}
