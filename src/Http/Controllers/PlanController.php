<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Resources\PlanResource;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class PlanController extends LaController
{
    protected string $model = Product::class;
    protected ?string $modelResource = PlanResource::class;

    public function baseQuery(Builder $query): Builder
    {
        return $query->where(function (Builder $q1) {
            $q1->whereHas('group', fn($q2) => $q2->where('type', GroupTypeEnum::PLAN))
                ->orWhereNull('group_id');
        })->orderByDesc('created_at');
    }
}
