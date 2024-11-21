<?php

namespace Aldeebhasan\LaSubscription\Http\Filters;

use Aldeebhasan\NaiveCrud\Contracts\FilterUI;
use Aldeebhasan\NaiveCrud\DTO\FilterField;
use Illuminate\Database\Eloquent\Builder;

class SearchFilter implements FilterUI
{
    public function fields(): array
    {
        return [
            /* @phpstan-ignore-next-line */
            new FilterField("q", callback: fn(Builder $query, $value) => $query->search($value)),
        ];
    }
}
