<?php

namespace Aldeebhasan\LaSubscription\Exceptions;

class SubscriptionRequiredExp extends \Exception
{
    /** @var string */
    protected $message = "You have to initialize subscription for this account first";
}
