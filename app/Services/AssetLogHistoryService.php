<?php

namespace App\Services;

use App\Repositories\AssetLogHistoryRepository;

/**
 * @property AssetLogHistoryRepository $repository
 * @mixin AssetLogHistoryRepository
 */
class AssetLogHistoryService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetLogHistoryRepository::class);
    }
}
