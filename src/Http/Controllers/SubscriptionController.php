<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Aldeebhasan\LaSubscription\Http\Filters\SearchFilter;
use Aldeebhasan\LaSubscription\Http\Resources\SubscriptionResource;
use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\LaSubscription\Models\Subscription;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionController extends LaController
{
    protected string $model = Subscription::class;
    protected ?string $modelResource = SubscriptionResource::class;

    /** @var string[] */
    protected array $filters = [SearchFilter::class];

    protected function showQuery(Builder $query): Builder
    {
        return $query->with(['plan', 'contracts', 'contracts.product', 'contracts.transactions', 'contracts.transactions.causative']);
    }

    public function statistics(Request $request): Response
    {
        $plans = Product::where(function (Builder $q1) {
            $q1->whereHas('group', fn($q2) => $q2->where('type', GroupTypeEnum::PLAN))
                ->orWhereNull('group_id');
        })->count();

        $plugins = Product::withWhereHas('group', fn($q2) => $q2->where('type', GroupTypeEnum::PLUGIN))->count();
        $features = Feature::count();
        $subscriptions = Subscription::count();

        $year = (int)$request->input('year', now()->year);
        $start = now()->setYear($year)->startOfYear();
        $end = now()->setYear($year)->endOfYear();

        $chartData = Subscription::query()->whereBetween('start_at', [$start, $end])
            ->get()
            ->groupBy(fn(Subscription $subscription) => carbonParse($subscription->start_at)->monthName)
            ->mapWithKeys(fn(Collection $subscriptions, string $monthName) => [$monthName => $subscriptions->count()]);

        return $this->success(compact('plans', 'plugins', 'features', 'subscriptions', 'chartData'));
    }
}
