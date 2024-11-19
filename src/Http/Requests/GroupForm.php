<?php

namespace Aldeebhasan\LaSubscription\Http\Requests;

use Aldeebhasan\NaiveCrud\Http\Requests\BaseRequest;

class GroupForm extends BaseRequest
{
    /** @return array<string,mixed> */
    public function storeRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'type' => ['required'],
        ];
    }

    /** @return array<string,mixed> */
    public function updateRules(): array
    {
        return $this->storeRules();
    }
}
