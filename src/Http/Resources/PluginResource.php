<?php

namespace Aldeebhasan\LaSubscription\Http\Resources;

use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\NaiveCrud\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class PluginResource extends BaseResource
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
            'price' => "$this->price Monthly | $this->price_yearly Yearly ",
            'group' => $this->group?->name ?? "-",
            'created_at' => carbonParse($this->created_at)->toDateTimeString(),
        ];
    }
}
