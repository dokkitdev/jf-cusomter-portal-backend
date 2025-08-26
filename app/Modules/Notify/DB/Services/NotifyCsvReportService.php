<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyCsvReportRepository;
use App\Services\BaseService;

/**
 * @property NotifyCsvReportRepository $repository
 * @mixin NotifyCsvReportRepository
 */
class NotifyCsvReportService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(NotifyCsvReportRepository::class);
    }
}
