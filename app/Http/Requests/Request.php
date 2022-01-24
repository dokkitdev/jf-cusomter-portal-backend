<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Services\UserService;
use RonasIT\Support\BaseRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Request extends BaseRequest
{
    public function getUserId()
    {
        return $this->user()->id;
    }

    public function isCustomer()
    {
        return $this->user()->role_id === Role::CUSTOMER;
    }

    public function validateEmailInsensitively($value, $userId = null, $attributeName = 'email')
    {
        $user = app(UserService::class)->getByEmailInsensitively($value);

        if ($user && (($userId === null) || ($user['id'] !== (int) $userId))) {
            throw new UnprocessableEntityHttpException(__('validation.exceptions.unique', ['attribute' => $attributeName]));
        }
    }

    public function validateExistsByPermissions($id, $entityName)
    {
        $service = app("App\Services\\{$entityName}Service");

        if ($this->isCustomer()) {
            $entity = $service->findByPermissions($id, $this->getUserId());
        } else {
            $entity = $service->find($id);
        }

        if (!$entity) {
            throw new NotFoundHttpException(__('validation.exceptions.not_found', ['entity' => $entityName]));
        }

        return $entity;
    }
}