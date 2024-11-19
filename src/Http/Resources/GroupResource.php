<?php

namespace Aldeebhasan\LaSubscription\Http\Resources;

use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\NaiveCrud\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class GroupResource extends BaseResource
{
    /** @return array<string,mixed> */
    public function toIndexArray(Request $request): array
    {
        /* @var Feature $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
