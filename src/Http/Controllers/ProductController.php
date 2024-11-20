<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends LaController
{
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
            'features' => $features,
        ]);
    }

    public function afterStoreHook(Request $request, Model $model): void
    {
        $features = Arr::wrap($request->features);
        $data = [];
        foreach ($features as $id => $pivot) {
            if ($pivot && $pivot['active']) {
                $data[$id] = [
                    'active' => true,
                    'value' => (int)($pivot['value'] ?? 0),
                ];
            }
        }
        /* @phpstan-ignore-next-line  */
        $model->features()->sync($data);
    }

    public function afterUpdateHook(Request $request, Model $model): void
    {
        $this->afterStoreHook($request, $model);
    }
}
