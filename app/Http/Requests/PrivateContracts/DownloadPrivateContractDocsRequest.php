<?php

namespace App\Http\Requests\PrivateContracts;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadPrivateContractDocsRequest extends BasePrivateContractTemplateRequest
{
    public function rules(): array
    {
        return [
            'filename' => 'required|string',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        if (!Storage::disk('private_contracts_docs')->exists($this->input('filename'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'File']));
        }
    }
}
