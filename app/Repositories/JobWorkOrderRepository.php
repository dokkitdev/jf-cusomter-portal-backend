<?php

namespace App\Repositories;

use App\Models\JobWorkOrder;

/**
 * @property JobWorkOrder $model
*/
class JobWorkOrderRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(JobWorkOrder::class);
    }
}
