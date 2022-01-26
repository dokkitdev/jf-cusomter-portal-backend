<?php

namespace App\Repositories;

use App\Models\SimproLog;

/**
 * @property SimproLog $model
*/
class SimproLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(SimproLog::class);
    }
}
