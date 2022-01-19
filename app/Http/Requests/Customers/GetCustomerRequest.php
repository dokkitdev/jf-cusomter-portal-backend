<?php

namespace App\Http\Requests\Customers;

use App\Http\Requests\Request;
use App\Models\Role;
use App\Services\CustomerService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetCustomerRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'with' => 'array',
            'with.*' => 'string|in:users'
        ];
    }

    public function validateResolved()
    {
        parent::validateResolved();

        $service = app(CustomerService::class);

        if (!$service->exists($this->route('id'))) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => 'Customer']));
        }
    }
}
