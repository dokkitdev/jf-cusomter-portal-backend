<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\UserService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetUserRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN) || ($this->user()->id === $this->route('id'));
    }

    public function rules(): array
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:customers'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(UserService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
        }
    }
}
