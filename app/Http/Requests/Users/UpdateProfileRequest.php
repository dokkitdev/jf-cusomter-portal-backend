<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class UpdateProfileRequest extends Request
{
    public function rules(): array
    {
        return [
            'old_password' => 'required_with:password|string|password',
            'password' => 'new_password|confirmed',
            'password_confirmation' => 'string',
            'email' => 'string|email',
            'name' => 'string',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        if ($this->has('email')) {
            $this->validateEmailInsensitively($this->get('email'), $this->user()->id);
        }
    }
}
