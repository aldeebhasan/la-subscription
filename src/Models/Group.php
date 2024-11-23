<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;
use Illuminate\Database\Eloquent\Builder;

class Group extends LaModel
{
    protected $fillable = ['name', 'type'];
    protected $casts = [
        'type' => GroupTypeEnum::class,
    ];

    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where('name', "like", "%$keyword%");
    }
}
