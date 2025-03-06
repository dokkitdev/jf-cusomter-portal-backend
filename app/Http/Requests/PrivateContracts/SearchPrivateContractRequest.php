<?php

namespace App\Http\Requests\PrivateContracts;

use App\Http\Requests\Request;

class SearchPrivateContractRequest extends Request
{
    public function rules(): array
    {
        return [
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',
            'all' => 'integer|nullable',
            'order_by' => 'string|nullable',
            'desc' => 'boolean|nullable',
        ];
    }
}
