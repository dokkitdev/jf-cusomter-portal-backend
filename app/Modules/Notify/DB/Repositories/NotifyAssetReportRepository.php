<?php

namespace App\Modules\Notify\DB\Repositories;

use App\Modules\Notify\DB\Models\NotifyAssetReport;
use App\Repositories\BaseRepository;

/**
 * @property NotifyAssetReport $model
*/
class NotifyAssetReportRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(NotifyAssetReport::class);
    }
}
