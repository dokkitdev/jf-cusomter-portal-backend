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
}
