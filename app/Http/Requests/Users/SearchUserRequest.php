<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;
use App\Models\Role;

class SearchUserRequest extends Request
{
    public function authorize(): bool
    {
        return $this->user()->role_id == Role::ADMIN;
    }

    public function rules(): array
    {
        return [
            'name_query' => 'string|nullable',
            'email_query' => 'string|nullable',
            'customer_ids' => 'array',
            'customer_ids.*' => 'integer',
            'role_ids' => 'array',
            'role_ids.*' => 'integer',
            'page' => 'integer|nullable',
            'per_page' => 'integer|nullable',
            'all' => 'integer|nullable',
            'query' => 'string|nullable',
            'order_by' => 'string|nullable',
            'desc' => 'boolean|nullable',
            'with' => 'array',
            'with.*' => 'string|in:customers'
        ];
    }
}
