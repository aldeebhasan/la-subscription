<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\NaiveCrud\Http\Controllers\BaseController;

class LaController extends BaseController
{
    protected bool $authorize = false;

    /** @var string[] */
    protected array $paginationMeta = ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total', 'links'];
}
