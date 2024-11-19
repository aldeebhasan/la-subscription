<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\NaiveCrud\Http\Controllers\BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LaController extends BaseController
{
    protected bool $authorize = false;

    /** @var string[] */
    protected array $paginationMeta = ['current_page', 'from', 'last_page', 'path', 'per_page', 'to', 'total', 'links'];

    public function findItem(Request $request, string|int $id): Model
    {
        $query = $this->baseQueryResolver($request)
            ->setExtendQuery($this->showQuery(...))
            ->build();

        /* @var Model */
        return $this->findShowItem($query, $id);
    }
}
