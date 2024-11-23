<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Requests\PlanForm;
use Aldeebhasan\LaSubscription\Http\Resources\PlanResource;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlanController extends ProductController
{
    protected ?string $modelResource = PlanResource::class;
    protected ?string $modelRequestForm = PlanForm::class;

    public function baseQuery(Builder $query): Builder
    {
        return $query->where(function (Builder $q1) {
            $q1->whereHas('group', fn($q2) => $q2->where('type', GroupTypeEnum::PLAN))
                ->orWhereNull('group_id');
        })->orderByDesc('created_at');
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
}
