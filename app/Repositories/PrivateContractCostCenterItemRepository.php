<?php

namespace App\Repositories;

use App\Models\PrivateContractCostCenterItem;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property PrivateContractCostCenterItem $model
 */
class PrivateContractCostCenterItemRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(PrivateContractCostCenterItem::class);
    }
}
