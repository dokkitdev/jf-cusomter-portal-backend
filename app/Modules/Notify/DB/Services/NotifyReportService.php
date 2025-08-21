<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyReportRepository;
use App\Services\BaseService;

/**
 * @property NotifyReportRepository $repository
 * @mixin NotifyReportRepository
 */
class NotifyReportService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(NotifyReportRepository::class);
    }
}
