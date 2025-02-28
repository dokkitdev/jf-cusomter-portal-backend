<?php

namespace App\Http\Requests\PrivateContracts;

use App\Http\Requests\Request;
use App\Models\PrivateContract;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BasePrivateContractTemplateRequest extends Request
{
    protected function checkTemplateType(): void
    {
        $type = $this->route('type');

        if (!in_array($this->route('type'), PrivateContract::TEMPLATE_TYPES)) {
            throw new UnprocessableEntityHttpException(__('validation.not_in', ['attribute' => $type]));
        }
    }
}
