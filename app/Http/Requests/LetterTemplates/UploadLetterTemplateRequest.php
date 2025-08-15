<?php

namespace App\Http\Requests\LetterTemplates;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\LetterTemplateService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UploadLetterTemplateRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
    }

    public function validateResolved(): void
    {
        parent::validateResolved();

        $this->checkExists();
    }

    protected function checkExists(): void
    {
        $exists = in_array($this->route('name'), LetterTemplateService::LETTER_NAMES);

        if (!$exists) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'LetterTemplate']));
        }
    }
}
