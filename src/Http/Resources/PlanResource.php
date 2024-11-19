<?php

namespace Aldeebhasan\LaSubscription\Http\Resources;

use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\NaiveCrud\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class PlanResource extends BaseResource
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
            'created_at' => carbonParse($this->created_at)->toDateTimeString(),
        ];
    }

    /** @return array<string,mixed> */
    public function toShowArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'active' => $this->active ? 1 : 0,
            'price' => "$this->price",
            'price_yearly' => "$this->price_yearly",
        ];
    }
}
