<?php

namespace App\Http\Requests\Documents;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateDocumentRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'media_id' => 'required|exists:media,id',
            'title' => 'string|nullable',
            'description' => 'string|nullable'
        ];
    }
}
