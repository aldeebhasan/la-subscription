<?php

namespace Aldeebhasan\LaSubscription\Facades;

use Aldeebhasan\LaSubscription\Concerns\SubscriberUI;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Aldeebhasan\LaSubscription\LaSubscription
 *
 * @method static LaSubscription make(SubscriberUI $subscriber)
 */
class LaSubscription extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Aldeebhasan\LaSubscription\LaSubscription::class;
    }
}
