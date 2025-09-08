<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyAssetReportValidationRepository;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function search(array $filters): LengthAwarePaginator
    {
        return $this
            ->searchQuery($filters)
            ->filterBy('site_id')
            ->filterByList('service_level_name', 'service_level_names')
            ->filterByList('asset_type', 'asset_types')
            ->filterByList('job_stage', 'job_stages')
            ->filterByList('error_type', 'error_types')
            ->getSearchResults();
    }
}
