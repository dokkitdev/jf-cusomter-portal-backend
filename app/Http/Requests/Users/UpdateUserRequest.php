<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\UserService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateUserRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id === Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'email' => 'string|email',
            'name' => 'string',
            'role_id' => 'integer|exists:roles,id',
            'customer_ids' => 'array',
            'customer_ids.*' => 'integer|exists:customers,id',
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(UserService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'User']));
        }

        if ($this->has('email')) {
            $this->validateEmailInsensitively($this->get('email'), $this->route('id'));
        }
    }
}
