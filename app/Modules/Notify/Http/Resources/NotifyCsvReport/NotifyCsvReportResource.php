<?php

namespace App\Modules\Notify\Http\Resources\NotifyCsvReport;

use App\Http\Resources\BaseResource;
use App\Modules\Notify\DB\Models\NotifyCsvReport;

/**
 * @property NotifyCsvReport $resource
 */
class NotifyCsvReportResource extends BaseResource
{
    public function toArray($request): array
    {
        $createdAt = $this->resource->created_at;
        $updatedAt = $this->resource->updated_at;

        return [
            'id' => $this->resource->id,
            'report_type' => __("notify::notify__csv_reports.report_types.{$this->resource->report_type}"),
            'file_name' => $this->resource->file_name,
            'is_finished' => $this->resource->is_finished,
            'created_at' => isset($createdAt) ? $createdAt->format(self::DATE_FORMAT) : null,
            'updated_at' => isset($updatedAt) ? $updatedAt->format(self::DATE_FORMAT) : null,
        ];
    }
}
