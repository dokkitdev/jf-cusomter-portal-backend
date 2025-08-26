<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\NotifyReportRepository;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

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
        return Storage::disk('pdf_reports')->path("{$id}.pdf");
    }
}
