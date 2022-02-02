<?php

namespace App\Repositories;

use App\Models\AssetTestRecord;

/**
 * @property AssetTestRecord $model
*/
class AssetTestRecordRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetTestRecord::class);
    }
}
