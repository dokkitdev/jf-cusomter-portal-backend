<?php

namespace App\Modules\Notify\Http\Resources\AssetReportValidation;

use App\Http\Resources\BaseResource;

/**
 * @property array $resource
 */
class AssetReportValidationSearchFiltersResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'site_ids' => $this->resource['site_ids'],
            'service_level_names' => $this->resource['service_level_names'],
            'asset_types' => $this->resource['asset_types'],
            'job_stages' => $this->resource['job_stages'],
            'error_types' => $this->resource['error_types'],
        ];
    }
}
