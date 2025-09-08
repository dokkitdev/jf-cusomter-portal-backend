<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyAssetReportValidationRepository;
use App\Services\BaseService;

/**
 * @property NotifyAssetReportValidationRepository $repository
 * @mixin NotifyAssetReportValidationRepository
 */
class NotifyAssetReportValidationService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(NotifyAssetReportValidationRepository::class);
    }
}
