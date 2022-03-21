<?php

namespace App\Repositories;

use App\Models\AssetTestRecord;
use Illuminate\Database\Eloquent\Model;

/**
 * @property AssetTestRecord $model
*/
class AssetTestRecordRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(AssetTestRecord::class);
    }

    public function getAssetTestRecordForReport(int $assetId): ?Model
    {
        return $this->getQuery()
            ->where('asset_id', $assetId)
            ->whereNotNull('job_id')
            ->orderBy('id')
            ->with(['job.customer', 'job.next_schedule', 'job.job_no_access_dates'])
            ->first();
    }
}
