<?php

namespace App\Modules\Notify\Http\Resources\AssetReportValidation;

use App\Http\Resources\BaseResource;
use App\Modules\Notify\DB\Models\NotifyAssetReportValidation;

/**
 * @property NotifyAssetReportValidation $resource
 */
class AssetReportValidationResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'site_id' => $this->resource->site_id,
            'asset_id' => $this->resource->asset_id,
            'uprn' => $this->resource->uprn,
            'asset_type' => $this->resource->asset_type,
            'service_level_name' => $this->resource->service_level_name,
            'job_stage' => $this->resource->job_stage,
            'error' => $this->resource->error,
            'created_at' => $this->renderDate($this->resource->created_at),
            'updated_at' => $this->renderDate($this->resource->updated_at),
        ];
    }
}
