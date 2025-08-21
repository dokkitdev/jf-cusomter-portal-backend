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
}
