<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Filters\SearchFilter;
use Aldeebhasan\LaSubscription\Http\Requests\FeatureForm;
use Aldeebhasan\LaSubscription\Http\Resources\FeatureResource;
use Aldeebhasan\LaSubscription\Http\Resources\GroupResource;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Group;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureController extends LaController
{
    protected string $model = Feature::class;
    protected ?string $modelResource = FeatureResource::class;
    protected ?string $modelRequestForm = FeatureForm::class;

    /** @var string[] */
    protected array $filters = [SearchFilter::class];

    public function baseQuery(Builder $query): Builder
    {
        return $query->with('group')->orderByDesc('created_at');
    }

    public function edit(Request $request, string|int $id): Response|Responsable
    {
        $item = $this->findItem($request, $id);
        $item = $this->formatShowItem($item);

        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => $item,
            'groups' => GroupResource::collection(Group::where('type', GroupTypeEnum::FEATURE)->get()),
        ]);
    }

    public function create(Request $request): Response|Responsable
    {
        return $this->showResponse(__('NaiveCrud::messages.success'), [
            'item' => null,
            'groups' => GroupResource::collection(Group::where('type', GroupTypeEnum::FEATURE)->get()),
        ]);
    }
}
