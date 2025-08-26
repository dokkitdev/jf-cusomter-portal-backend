<?php

namespace App\Modules\Notify\DB\Services;

use App\Modules\Notify\DB\Repositories\ParsingLogRepository;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property ParsingLogRepository $repository
 * @mixin ParsingLogRepository
 */
class ParsingLogService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(ParsingLogRepository::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this
            ->searchQuery($filters)
            ->getSearchResults();
    }
}
