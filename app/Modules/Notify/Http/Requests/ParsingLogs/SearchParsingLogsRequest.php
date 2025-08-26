<?php

namespace App\Modules\Notify\Http\Requests\ParsingLogs;

use App\Http\Requests\Request;
use App\Models\Role;

class SearchParsingLogsRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }

    public function rules(): array
    {
        return [
            'all' => 'filled|boolean',
            'page' => 'filled|integer',
            'per_page' => 'filled|integer',
            'order_by' => 'filled|string|in:parsing_type,parsing_date,created_at',
            'desc' => 'filled|boolean',
        ];
    }
}
