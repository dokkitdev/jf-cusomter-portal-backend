<?php

namespace App\Http\Requests\PrivateContracts;

use App\Http\Requests\Request;

class GeneratePrivateContractLettersRequest extends Request
{
    public function rules(): array
    {
        return [
            'private_contract_ids' => 'required|array',
            'private_contract_ids.*' => 'required|exists:private_contracts,id',
        ];
    }
}
