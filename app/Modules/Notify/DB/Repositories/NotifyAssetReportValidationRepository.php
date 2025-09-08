<?php

namespace App\Modules\Notify\DB\Repositories;

use App\Modules\Notify\DB\Models\NotifyAssetReportValidation;
use App\Repositories\BaseRepository;

/**
 * @property NotifyAssetReportValidation $model
*/
class NotifyAssetReportValidationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(NotifyAssetReportValidation::class);
    }
}
