<?php

namespace Aldeebhasan\LaSubscription\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aldeebhasan\LaSubscription\LaSubssription
 */
class LaSubssription extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aldeebhasan\LaSubscription\LaSubssription::class;
    }
}
