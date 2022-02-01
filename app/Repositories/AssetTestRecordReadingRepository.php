<?php

namespace App\Repositories;

use App\Models\AssetTestRecordReading;

/**
 * @property AssetTestRecordReading $model
*/
class AssetTestRecordReadingRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetTestRecordReading::class);
    }
}
