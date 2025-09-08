<?php

namespace App\Modules\Notify\Http\Requests\AssetReportValidations;

use App\Http\Requests\Request;
use App\Models\Role;

class GetAssetReportValidationSearchFiltersRequest extends Request
{
    public function authorize(): bool
    {
        return ($this->user()->role_id === Role::ADMIN);
    }

    public function rules(): array
    {
        return [
            'site_id' => 'nullable|integer',
        ];
    }
}
