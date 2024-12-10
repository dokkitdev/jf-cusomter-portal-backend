<?php

namespace App\Http\Requests\AuthenticationCodes;

use App\Http\Requests\Request;

class SendAuthenticationCodeRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }
}
