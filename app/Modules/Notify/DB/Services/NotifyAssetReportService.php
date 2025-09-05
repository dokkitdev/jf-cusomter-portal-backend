<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyAssetReportRepository;
use App\Services\BaseService;

/**
 * @property NotifyAssetReportRepository $repository
 * @mixin NotifyAssetReportRepository
 */
class NotifyAssetReportService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(NotifyAssetReportRepository::class);
    }
}
