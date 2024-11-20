<?php

namespace Aldeebhasan\LaSubscription\Http\Requests;

use Aldeebhasan\LaSubscription\Models\Product;
use Aldeebhasan\NaiveCrud\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class PlanForm extends BaseRequest
{
    /** @return array<string,mixed> */
    public function storeRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'code' => ['required', 'string',  Rule::unique((new Product)->getTable())],
            'price' => ['required', 'numeric', 'min:0'],
            'price_yearly' => ['required', 'numeric', 'min:0'],
            'active' => ['required', 'boolean'],
            'features' => ['nullable', 'array'],
        ];
    }

    /** @return array<string,mixed> */
    public function updateRules(): array
    {
        $rules = $this->storeRules();
        $rules['code'] = ['required', 'string', Rule::unique((new Product)->getTable())->ignore($this->request->get('id'))];

        return $rules;
    }
}
