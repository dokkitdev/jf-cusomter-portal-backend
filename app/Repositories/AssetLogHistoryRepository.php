<?php

namespace App\Repositories;

use App\Models\AssetLogHistory;

/**
 * @property AssetLogHistory $model
*/
class AssetLogHistoryRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetLogHistory::class);
    }

    public function last()
    {
        return $this->getQuery()
            ->orderBy('id', 'desc')
            ->first();
    }
}
