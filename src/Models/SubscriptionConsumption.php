<?php

namespace Aldeebhasan\LaSubscription\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $end_at
 * @property Feature $feature
 */
class SubscriptionConsumption extends LaModel
{
    protected $fillable = ['subscription_id', 'code', 'limited', 'feature_id', 'quota', 'consumed', 'end_at'];
    protected $casts = [
        'quota' => 'double',
        'consumed' => 'double',
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
                $query->whereNull('end_at')->orWhere('end_at', '>=', now());
            })->orWhere(function (Builder $limited) {
                $limited->where('limited', true)->whereColumn('quota', '>', 'consumed')
                    ->where(function (Builder $query) {
                        $query->whereNull('end_at')->orWhere('end_at', '>=', now());
                    });
            });
        });
    }
}
