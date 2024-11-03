<?php

namespace Aldeebhasan\LaSubscription\Exceptions;

class SwitchToSamePlanExp extends \Exception
{
    /** @var string */
    protected $message = "You are note allowed to switch from & to the same plan";
}
