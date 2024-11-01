<?php

namespace Aldeebhasan\LaSubscription\Models;

use Aldeebhasan\LaSubscription\Enums\GroupTypeEnum;

class Group extends LaModel
{
    protected $fillable = ['name', 'type'];
    protected $casts = [
        'type' => GroupTypeEnum::class,
    ];
}
