<?php

namespace Aldeebhasan\LaSubscription\Http\Requests;

use Aldeebhasan\LaSubscription\Models\Feature;
use Aldeebhasan\NaiveCrud\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class FeatureForm extends BaseRequest
{
    /** @return array<string,mixed> */
    public function storeRules(): array
    {
        return [
            'group_id' => ['required', 'numeric'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'code' => ['required', 'string',  Rule::unique((new Feature)->getTable())],
            'active' => ['required', 'boolean'],
            'limited' => ['required', 'boolean'],
        ];
    }

    /** @return array<string,mixed> */
    public function updateRules(): array
    {
        $rules = $this->storeRules();
        $rules['code'] = ['required', 'string', Rule::unique((new Feature)->getTable())->ignore($this->request->get('id'))];

        return $rules;
    }
}
