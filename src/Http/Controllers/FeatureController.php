<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Http\Resources\FeatureResource;
use Aldeebhasan\LaSubscription\Models\Feature;
use Illuminate\Database\Eloquent\Builder;

class FeatureController extends LaController
{
    protected string $model = Feature::class;
    protected ?string $modelResource = FeatureResource::class;

    public function baseQuery(Builder $query): Builder
    {
        return $query->with('group')->orderByDesc('created_at');
    }
}
