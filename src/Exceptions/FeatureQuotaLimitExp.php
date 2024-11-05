<?php

namespace Aldeebhasan\LaSubscription\Exceptions;

class FeatureQuotaLimitExp extends \Exception
{
    /** @var string */
    protected $message = "The feature you are trying to access exceeds the allowed quota";
}
