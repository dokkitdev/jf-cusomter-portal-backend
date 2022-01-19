<?php

namespace App\Services;

use App\Repositories\DocumentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property DocumentRepository $repository
 * @mixin DocumentRepository
 */
class DocumentService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(DocumentRepository::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByTitle()
            ->filterFrom('created_at', false, 'created_at_from')
            ->filterTo('created_at', false, 'created_at_to')
            ->filterByQuery(['description'])
            ->with(Arr::get($filters, 'with', []))
            ->getSearchResults();
    }
}
