<?php

namespace App\Modules\Notify\Http\Requests\AssetReports;

use App\Http\Requests\Request;
use App\Models\Role;

class CreateAssetReportRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }
}
