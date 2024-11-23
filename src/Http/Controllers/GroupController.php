<?php

namespace Aldeebhasan\LaSubscription\Http\Controllers;

use Aldeebhasan\LaSubscription\Http\Filters\SearchFilter;
use Aldeebhasan\LaSubscription\Http\Requests\GroupForm;
use Aldeebhasan\LaSubscription\Http\Resources\GroupResource;
use Aldeebhasan\LaSubscription\Models\Group;

class GroupController extends LaController
{
    protected string $model = Group::class;
    protected ?string $modelResource = GroupResource::class;
    protected ?string $modelRequestForm = GroupForm::class;

    /** @var string[] */
    protected array $filters = [SearchFilter::class];
}
