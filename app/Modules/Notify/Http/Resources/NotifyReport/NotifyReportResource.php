<?php

namespace App\Modules\Notify\Http\Resources\NotifyReport;

use App\Http\Resources\BaseResource;
use App\Modules\Notify\DB\Models\NotifyReport;

/**
 * @property NotifyReport $resource
 */
class NotifyReportResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'report_type' => __("notify::notify_reports.report_types.{$this->resource->report_type}"),
            'letters_generated' => $this->resource->letters_generated,
            'is_finished' => $this->resource->is_finished,
            'created_at' => $this->renderDate($this->resource->created_at),
            'updated_at' => $this->renderDate($this->resource->updated_at)
        ];
    }
}
