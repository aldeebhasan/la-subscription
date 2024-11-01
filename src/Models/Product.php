<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Concerns\ContractUI;
use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
            "{$prefix}__product_feature",
            "product_id",
            "feature_id",
        )->withPivot('value', 'active');
    }

    public function getId(): int|string
    {
        return $this->getKey();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function isRecurring(): bool
    {
        return $this->type === BillingCycleEnum::RECURRING;
    }
}
