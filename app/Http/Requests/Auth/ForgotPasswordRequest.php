<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;
use App\Services\UserService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ForgotPasswordRequest extends Request
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|email'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $user = app(UserService::class)->getByEmailInsensitively($this->get('email'));

        if (!$user) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
        }
    }
}
