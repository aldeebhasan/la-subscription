<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Traits\ValidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ?Carbon $end_at
 * @property Feature $feature
 */
class SubscriptionQuota extends LaModel
{
    use ValidTrait;

    protected $fillable = ['subscription_id', 'code', 'limited', 'feature_id', 'quota', 'consumed', 'end_at'];
    protected $casts = [
        'quota' => 'double',
        'consumed' => 'double',
        'end_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function isActive(): bool
    {
        return (!$this->limited || ($this->consumed < $this->quota)) && $this->isValid();
    }

    public function scopeActive(Builder $builder): Builder
    {
        /* @phpstan-ignore-next-line  */
        return $builder->valid()->where(function (Builder $query) {
            $query->where('limited', false)
                ->orWhere(function (Builder $limited) {
                    $limited->where('limited', true)
                        ->whereColumn('quota', '>', 'consumed');
                });
        });
    }
}
