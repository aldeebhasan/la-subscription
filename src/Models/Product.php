<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<Feature> $features
 */
class Product extends LaModel implements ContractUI
{
    protected $fillable = ['name', 'code', 'description', 'group_id', 'active', 'type', 'price', 'price_yearly'];
    protected $casts = [
        'active' => 'bool',
        'type' => BillingCycleEnum::class,
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class)->withDefault(function (Group $group) {
            $group->setAttribute('name', 'Others');
        });
    }

    public function features(): BelongsToMany
    {
        $prefix = config('subscription.prefix');

        return $this->belongsToMany(
            Feature::class,
            "{$prefix}_product_feature",
            "product_id",
            "feature_id",
        )->withPivot('value', 'active');
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isRecurring(): bool
    {
        return $this->type === BillingCycleEnum::RECURRING;
    }

    public function isActive(): bool
    {
        return (bool)$this->active;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function getFeatures(): Collection
    {
        $this->loadMissing('features');

        return $this->features;
    }
}
