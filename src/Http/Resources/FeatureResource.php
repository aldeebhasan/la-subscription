<?php

namespace Aldeebhasan\LaSubscription\Http\Resources;

use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\NaiveCrud\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class FeatureResource extends BaseResource
{
    /** @return array<string,mixed> */
    public function toIndexArray(Request $request): array
    {
        /* @var Product $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'active' => (bool)$this->active,
            'group' => $this->group?->name ?? "-",
            'created_at' => carbonParse($this->created_at)->toDateTimeString(),
        ];
    }

    /** @return array<string,mixed> */
    public function toShowArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'active' => $this->active ? 1 : 0,
            'limited' => $this->limited ? 1 : 0,
        ];
    }
}
