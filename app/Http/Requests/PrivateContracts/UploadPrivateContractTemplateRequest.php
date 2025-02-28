<?php

namespace App\Http\Requests\PrivateContracts;

class UploadPrivateContractTemplateRequest extends BasePrivateContractTemplateRequest
{
    public function rules(): array
    {
        $types = implode(',', config('defaults.private_contract.templates.permitted_template_types'));

        return [
            'template' => "file|required|max:10240|mimes:{$types}"
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->checkTemplateType();
    }
}
