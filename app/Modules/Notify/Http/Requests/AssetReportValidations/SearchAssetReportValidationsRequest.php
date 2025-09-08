<?php

namespace App\Modules\Notify\Http\Requests\AssetReportValidations;

use App\Http\Requests\Request;
use App\Models\Role;

class SearchAssetReportValidationsRequest extends Request
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
            'order_by' => 'filled|string|in:id,site_id,uprn,asset_id,asset_type,service_level_name,error',
            'desc' => 'filled|boolean',
        ];
    }
}
