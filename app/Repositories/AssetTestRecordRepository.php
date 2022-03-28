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
        $assetTestRecord = $this->getQuery()
            ->where('asset_id', $assetId)
            ->whereNotNull('job_id')
            ->whereIn('result', [AssetTestRecord::RESULT_PASS, AssetTestRecord::RESULT_FAIL])
            ->orderBy('id')
            ->with(['job.customer', 'job.next_schedule', 'job.job_no_access_dates'])
            ->first();

        if (!$assetTestRecord) {
            $assetTestRecord = $this->getQuery()
                ->where('asset_id', $assetId)
                ->whereNotNull('job_id')
                ->whereIn('result', [AssetTestRecord::RESULT_NO_TEST])
                ->orderBy('id')
                ->with(['job.customer', 'job.next_schedule', 'job.job_no_access_dates'])
                ->first();
        }

        return $assetTestRecord;
    }
}
