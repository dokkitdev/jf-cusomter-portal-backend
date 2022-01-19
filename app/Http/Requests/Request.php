<?php

namespace App\Http\Requests;

use App\Services\UserService;
use RonasIT\Support\BaseRequest;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Request extends BaseRequest
{
    public function validateEmailInsensitively($value, $userId = null, $attributeName = 'email')
    {
        $user = app(UserService::class)->getByEmailInsensitively($value);

        if ($user && (($userId === null) || ($user['id'] !== (int) $userId))) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.unique', ['attribute' => $attributeName]));
        }
    }
}