<?php

namespace Aldeebhasan\LaSubscription\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aldeebhasan\LaSubscription\LaSubscription
 */
class LaSubssription extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aldeebhasan\LaSubscription\LaSubscription::class;
    }
}
