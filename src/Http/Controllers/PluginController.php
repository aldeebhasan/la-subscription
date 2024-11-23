<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Requests\PluginForm;
use Aldeebhasan\LaSubscription\Http\Resources\GroupResource;
use Aldeebhasan\LaSubscription\Http\Resources\PluginResource;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Group;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PluginController extends ProductController
{
    protected ?string $modelResource = PluginResource::class;
    protected ?string $modelRequestForm = PluginForm::class;

    public function baseQuery(Builder $query): Builder
    {
        return $query->withWhereHas('group', fn($q2) => $q2->where('type', GroupTypeEnum::PLUGIN))
            ->orderByDesc('created_at');
    }

    protected function showQuery(Builder $query): Builder
    {
        return $query->with('features');
    }

    public function edit(Request $request, string|int $id): Response|Responsable
    {
        /** @var Product $item */
        $item = $this->findItem($request, $id);
        $itemFormated = $this->formatShowItem($item);
        $features = Feature::with('group')->active()->get()
            ->map(function (Feature $feature) use ($item) {
                $productFeature = $item->features->firstWhere('id', $feature->id);

                return [
                    'id' => $feature->id,
                    'name' => $feature->name,
                    'group_name' => $feature->group?->name ?? "-",
                    'active' => $productFeature?->pivot->active ?? false,
                    'value' => $productFeature?->pivot->value ?? null,
                    'limited' => $feature->limited,

                ];
            })->groupBy('group_name')->toArray();

        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => $itemFormated,
            'groups' => GroupResource::collection(Group::where('type', GroupTypeEnum::PLUGIN)->get()),
            'features' => $features,
        ]);
    }

    public function create(Request $request): Response|Responsable
    {
        $features = Feature::with('group')->active()->get()
            ->map(fn(Feature $feature) => [
                'id' => $feature->id,
                'name' => $feature->name,
                'group_name' => $feature->group?->name ?? "-",
                'active' => false,
                'value' => null,
                'limited' => $feature->limited,
            ])->groupBy('group_name')->toArray();

        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => null,
            'groups' => GroupResource::collection(Group::where('type', GroupTypeEnum::PLUGIN)->get()),
            'features' => $features,
        ]);
    }
}
