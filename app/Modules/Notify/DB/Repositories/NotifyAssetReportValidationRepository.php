<?php

namespace App\Modules\Notify\DB\Repositories;

use App\Modules\Notify\DB\Models\NotifyAssetReportValidation;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property NotifyAssetReportValidation $model
*/
class NotifyAssetReportValidationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(NotifyAssetReportValidation::class);
    }

    public function getFieldValues(string $field, ?int $siteId = null): array
    {
        return $this
            ->getQuery()
            ->select($field)
            ->whereNotNull($field)
            ->when(isset($siteId), function (Builder $query) use ($siteId) {
                $query->where('site_id', $siteId);
            })
            ->groupBy($field)
            ->get()
            ->pluck($field)
            ->sort()
            ->toArray();
    }
}
