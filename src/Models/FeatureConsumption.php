<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\ConsumptionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $end_at
 * @property Feature $feature
 */
class FeatureConsumption extends LaModel
{
    protected $fillable = ['subscription_id', 'code', 'feature_id', 'consumed', 'type'];
    protected $casts = [
        'quota' => 'double',
        'consumed' => 'double',
        'type' => ConsumptionTypeEnum::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function scopeValid(Builder $builder, string|Carbon $startAt, string|Carbon $endAt): Builder
    {
        return $builder->whereDate("created_at", '>=', $startAt)
            ->whereDate("created_at", '<=', $endAt);
    }
}
