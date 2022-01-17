<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateUserRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|required',
            'email' => 'required|email',
            'role_id' => 'integer|exists:roles,id',
            'customer_ids' => 'array',
            'customer_ids.*' => 'integer|exists:customers,id',
            'is_send_email' => 'boolean'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $this->validateEmailInsensitively($this->get('email'));
    }
}
