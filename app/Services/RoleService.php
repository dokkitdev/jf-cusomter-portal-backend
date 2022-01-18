<?php

namespace App\Services;

use App\Repositories\RoleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * @property RoleRepository $repository
 * @mixin RoleRepository
 */
class RoleService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(RoleRepository::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterByQuery(['name'])
            ->getSearchResults();
    }
}
