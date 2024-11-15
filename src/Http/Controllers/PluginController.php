<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Resources\PluginResource;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class PluginController extends LaController
{
    protected string $model = Product::class;
    protected ?string $modelResource = PluginResource::class;

    public function baseQuery(Builder $query): Builder
    {
        return $query->withWhereHas('group', fn($q2) => $q2->where('type', GroupTypeEnum::PLUGIN))
            ->orderByDesc('created_at');
    }
}
