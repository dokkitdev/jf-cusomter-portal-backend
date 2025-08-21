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
        $createdAt = $this->resource->created_at;
        $updatedAt = $this->resource->updated_at;

        return [
            'id' => $this->resource->id,
            'report_type' => __("notify::notify_reports.report_types.{$this->resource->report_type}"),
            'letters_generated' => $this->resource->letters_generated,
            'is_finished' => $this->resource->is_finished,
            'created_at' => isset($createdAt) ? $createdAt->format(self::DATE_FORMAT) : null,
            'updated_at' => isset($updatedAt) ? $updatedAt->format(self::DATE_FORMAT) : null,
        ];
    }
}
