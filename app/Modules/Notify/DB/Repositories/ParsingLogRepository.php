<?php

namespace App\Modules\Notify\DB\Repositories;

use App\Modules\Notify\DB\Models\ParsingLog;
use App\Repositories\BaseRepository;

/**
 * @property ParsingLog $model
*/
class ParsingLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(ParsingLog::class);
    }
}
