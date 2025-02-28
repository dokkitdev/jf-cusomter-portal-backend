<?php

namespace App\Http\Requests\PrivateContracts;

class DownloadPrivateContractTemplateRequest extends BasePrivateContractTemplateRequest
{
    public function validateResolved()
    {
        parent::validateResolved();

        $this->checkTemplateType();
    }
}
