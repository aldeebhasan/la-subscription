<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\BillingCycleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = ['name', 'code', 'description', 'group_id', 'active', 'type', 'price', 'price_yearly'];
    protected $casts = [
        'active' => 'bool',
        'type' => BillingCycleEnum::class,
    ];

    public function getTable(): string
    {
        $prefix = config('subscription.prefix');
        $table = parent::getTable();

        return "{$prefix}_{$table}";
    }

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
}
