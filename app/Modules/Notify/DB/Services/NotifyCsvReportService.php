<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyCsvReportRepository;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function search(array $filters): LengthAwarePaginator
    {
        return $this
            ->searchQuery($filters)
            ->getSearchResults();
    }
}
