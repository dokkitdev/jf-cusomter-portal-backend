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
            ->getSearchResults();
    }
}
