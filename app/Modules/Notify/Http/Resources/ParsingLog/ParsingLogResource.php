<?php

namespace App\Modules\Notify\Http\Resources\ParsingLog;

use App\Http\Resources\BaseResource;
use App\Modules\Notify\DB\Models\ParsingLog;
use Exception;

/**
 * @property ParsingLog $resource
 */
class ParsingLogResource extends BaseResource
{
    public function toArray($request): array
    {
        $createdAt = $this->resource->created_at;
        $updatedAt = $this->resource->updated_at;

        return [
            'id' => $this->resource->id,
            'parsing_type' => __("notify::parsing_logs.parsing_types.{$this->resource->parsing_type}"),
            'parsing_date' => $this->resource->parsing_date->format('Y-m-d'),
            'total_count' => $this->resource->total_count,
            'success_count' => $this->resource->success_count,
            'ids' => $this->resource->ids,
            'error_reasons' => $this->renderErrorReasons($this->resource->parsing_type, $this->resource->error_reasons),
            'created_at' => isset($createdAt) ? $createdAt->format(self::DATE_FORMAT) : null,
            'updated_at' => isset($updatedAt) ? $updatedAt->format(self::DATE_FORMAT) : null,
        ];
    }

    protected function renderErrorReasons(string $parsingType, array $errorReasons): array
    {
        switch ($parsingType) {
            case ParsingLog::PARSING_TYPE_WAREHOUSE_REPORT:
                return array_map(function (array $errorReason) {
                    return __("notify::parsing_logs.error_reasons.{$errorReason['reason']}", ['job_id' => $errorReason['job_id']]);
                }, $errorReasons);
            default:
                throw new Exception('Unknown parsing type');
        }
    }
}
