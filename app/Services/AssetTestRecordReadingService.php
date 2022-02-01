<?php

namespace App\Services;

use App\Repositories\AssetTestRecordReadingRepository;

/**
 * @property AssetTestRecordReadingRepository $repository
 * @mixin AssetTestRecordReadingRepository
 */
class AssetTestRecordReadingService extends BaseService
{
    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetTestRecordReadingRepository::class);
    }
}
