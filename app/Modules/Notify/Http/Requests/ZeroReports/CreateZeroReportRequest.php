<?php

namespace App\Modules\Notify\Http\Requests\ZeroReports;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateZeroReportRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }

    public function rules(): array
    {
        return [
            'date_from' => 'required|date_format:Y-m-d|lte:date_to',
            'date_to' => 'required|date_format:Y-m-d|gte:date_from',
        ];
    }
}
