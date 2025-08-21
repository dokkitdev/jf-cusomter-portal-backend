<?php

namespace App\Modules\Notify\Http\Requests\LetterTemplates;

use App\Http\Requests\Request;
use App\Models\Role;

class GetGroupedLetterTemplatesRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }
}
