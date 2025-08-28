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
        return [
            'id' => $this->resource->id,
            'report_type' => __("notify::notify__csv_reports.report_types.{$this->resource->report_type}"),
            'file_name' => $this->resource->file_name,
            'is_finished' => $this->resource->is_finished,
            'created_at' => $this->renderDate($this->resource->created_at),
            'updated_at' => $this->renderDate($this->resource->updated_at)
        ];
    }
}
