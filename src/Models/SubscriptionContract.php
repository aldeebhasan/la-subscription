<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Aldeebhasan\LaSubscription\Observers\SubscriptionContractObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Carbon $start_at
 * @property ?Carbon $end_at
 * @property Product $product
 *
 * @method Builder valid()
 */
#[ObservedBy(SubscriptionContractObserver::class)]
class SubscriptionContract extends LaModel
{
    protected $fillable = ['subscription_id', 'code', 'number', 'product_type', 'product_id', 'start_at', 'end_at', 'type', 'auto_renew'];
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

    public function product(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeValid(Builder $builder): Builder
    {
        return $builder->where(function (Builder $query) {
            $query->whereNull('end_at')
                ->orWhere('end_at', '>=', now());
        });
    }

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->where('auto_renew', true);
    }
}
