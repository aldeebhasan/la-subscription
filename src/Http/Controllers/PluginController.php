<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Requests\PluginForm;
use Aldeebhasan\LaSubscription\Http\Resources\FeatureResource;
use Aldeebhasan\LaSubscription\Http\Resources\GroupResource;
use Aldeebhasan\LaSubscription\Http\Resources\PluginResource;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Group;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PluginController extends LaController
{
    protected string $model = Product::class;
    protected ?string $modelResource = PluginResource::class;
    protected ?string $modelRequestForm = PluginForm::class;

    public function baseQuery(Builder $query): Builder
    {
        return $query->withWhereHas('group', fn($q2) => $q2->where('type', GroupTypeEnum::PLUGIN))
            ->orderByDesc('created_at');
    }

    public function edit(Request $request, string|int $id): Response|Responsable
    {
        $item = $this->findItem($request, $id);
        $item = $this->formatShowItem($item);

        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => $item,
            'groups' => GroupResource::collection(Group::where('type', GroupTypeEnum::PLUGIN)->get()),
            'features' => FeatureResource::collection(Feature::with('group')->active()->get()),
        ]);
    }

    public function create(Request $request): Response|Responsable
    {
        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => null,
            'groups' => GroupResource::collection(Group::where('type', GroupTypeEnum::PLUGIN)->get()),
            'features' => FeatureResource::collection(Feature::with('group')->active()->get()),
        ]);
    }
}
