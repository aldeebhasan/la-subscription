<?php

namespace Aldeebhasan\LaSubscription\Exceptions;

class PlanRequiredExp extends \Exception
{
    /** @var string */
    protected $message = "You have to specify the subscription plan first";
}
