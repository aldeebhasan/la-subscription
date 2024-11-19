<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Requests\PlanForm;
use Aldeebhasan\LaSubscription\Http\Resources\FeatureResource;
use Aldeebhasan\LaSubscription\Http\Resources\PlanResource;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Product;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PlanController extends LaController
{
    protected string $model = Product::class;
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
        $item = $this->findItem($request, $id);
        $item = $this->formatShowItem($item);

        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => $item,
            'features' => FeatureResource::collection(Feature::with('group')->active()->get()),
        ]);
    }

    public function create(Request $request): Response|Responsable
    {
        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => null,
            'features' => FeatureResource::collection(Feature::with('group')->active()->get()),
        ]);
    }
}
