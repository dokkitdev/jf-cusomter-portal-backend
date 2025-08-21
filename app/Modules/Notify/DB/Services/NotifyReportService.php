<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyReportRepository;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function search(array $filters): LengthAwarePaginator
    {
        return $this
            ->searchQuery($filters)
            ->getSearchResults();
    }

    public function getLettersPdfFilePath(int $id): string
    {
        //TODO: implement
        return storage_path('report_letters_pdf_sample.pdf');
    }
}
