<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\DocumentService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateDocumentRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'title' => 'string|nullable',
            'description' => 'string|nullable'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(DocumentService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Document']));
        }
    }
}
