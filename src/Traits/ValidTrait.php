<?php

namespace Aldeebhasan\LaSubscription\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ValidTrait
{
    public function isValid(): bool
    {
        $graceDays = config('subscription.grace_period', 0);

        return $this->start_at->lte(now()) && (is_null($this->end_at) || $this->end_at->addDays($graceDays)->gte(now()));
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->whereDate('start_at', '<=', now())
            ->where(function (Builder $query) {
                $query->whereNull('end_at')->orWhereDate(gracedEndDateColumn(), '>=', now());
            });
    }
}
