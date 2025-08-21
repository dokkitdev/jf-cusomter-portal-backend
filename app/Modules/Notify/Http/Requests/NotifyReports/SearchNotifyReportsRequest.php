<?php

namespace App\Modules\Notify\Http\Requests\NotifyReports;

use App\Http\Requests\Request;
use App\Models\Role;

class SearchNotifyReportsRequest extends Request
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
            'order_by' => 'filled|string|in:created_at,report_type,letters_generated',
            'desc' => 'filled|boolean',
        ];
    }
}
