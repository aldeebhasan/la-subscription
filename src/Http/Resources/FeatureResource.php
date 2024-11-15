<?php

namespace Aldeebhasan\LaSubscription\Http\Resources;

use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\NaiveCrud\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class FeatureResource extends BaseResource
{
    /** @return array<string,mixed> */
    public function toIndexArray(Request $request): array
    {
        /* @var Feature $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'active' => (bool)$this->active,
            'group' => $this->group?->name ?? "-",
            'limited' => (bool)$this->limited,
            'created_at' => carbonParse($this->created_at)->toDateTimeString(),
        ];
    }
}
