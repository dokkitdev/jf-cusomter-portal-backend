<?php

namespace App\Modules\Notify\DB\Repositories;

use App\Modules\Notify\DB\Models\NotifyReport;
use App\Repositories\BaseRepository;

/**
 * @property NotifyReport $model
*/
class NotifyReportRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(NotifyReport::class);
    }

    public function existsNotFinishedByReportTypes(array $reportTypes): bool
    {
        return $this->getQuery()
            ->whereIn('report_type', $reportTypes)
            ->where('is_finished', false)
            ->exists();
    }

    public function existsFinishedById(int $id): bool
    {
        return $this->getQuery()
            ->where('id', $id)
            ->where('is_finished', true)
            ->exists();
    }
}
