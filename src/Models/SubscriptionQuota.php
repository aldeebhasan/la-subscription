<?php

namespace Aldeebhasan\LaSubscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property ?Carbon $end_at
 * @property Feature $feature
 */
class SubscriptionQuota extends LaModel
{
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

    public function scopeValid(Builder $builder): Builder
    {
        return $builder->where(function (Builder $query) {
            $query->where('limited', false)->where(function (Builder $query) {
                $query->whereNull('end_at')->orWhere(gracedEndDateColumn(), '>=', now());
            })->orWhere(function (Builder $limited) {
                $limited->where('limited', true)->whereColumn('quota', '>', 'consumed')
                    ->where(function (Builder $query) {
                        $graceDays = config('subscription.grace_period', 0);
                        $query->whereNull('end_at')->orWhere(gracedEndDateColumn(), '>=', now());
                    });
            });
        });
    }

    public function canUse(): bool
    {
        return (!$this->limited || ($this->consumed < $this->quota)) && $this->active();
    }

    public function active(): bool
    {
        $graceDays = config('subscription.grace_period', 0);

        return !$this->end_at || $this->end_at->addDays($graceDays)->gte(now());
    }
}
