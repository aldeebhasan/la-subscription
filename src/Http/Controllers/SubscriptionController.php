<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Http\Resources\SubscriptionResource;
use Aldeebhasan\LaSubscription\Models\Subscription;

class SubscriptionController extends LaController
{
    protected string $model = Subscription::class;
    protected ?string $modelResource = SubscriptionResource::class;
}
