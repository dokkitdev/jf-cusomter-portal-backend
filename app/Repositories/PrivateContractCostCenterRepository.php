<?php

namespace App\Repositories;

use App\Models\PrivateContractCostCenter;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property PrivateContractCostCenter $model
 */
class PrivateContractCostCenterRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(PrivateContractCostCenter::class);
    }
}
