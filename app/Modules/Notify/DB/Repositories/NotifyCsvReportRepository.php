<?php

namespace App\Modules\Notify\DB\Repositories;

use App\Modules\Notify\DB\Models\NotifyCsvReport;
use App\Repositories\BaseRepository;

/**
 * @property NotifyCsvReport $model
*/
class NotifyCsvReportRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(NotifyCsvReport::class);
    }

    public function existsFinishedById(int $id): bool
    {
        return $this->getQuery()
            ->where('id', $id)
            ->where('is_finished', true)
            ->exists();
    }
}
