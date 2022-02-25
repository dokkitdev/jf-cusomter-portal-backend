<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\AssetTestRecordRepository;
use Illuminate\Support\Arr;

/**
 * @property AssetTestRecordRepository $repository
 * @mixin AssetTestRecordRepository
 */
class AssetTestRecordService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected JobService $jobService;
    protected AssetTestRecordReadingService $assetTestRecordReadingService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(AssetTestRecordRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->jobService = app(JobService::class);
        $this->assetTestRecordReadingService = app(AssetTestRecordReadingService::class);
    }

    public function syncByAsset(int $companyId, int $simproSiteId, int $simproAssetId, int $assetId): void
    {
        $testHistories = $this->simproClient->getAssetTestHistories($companyId, $simproSiteId, $simproAssetId);

        $this->repository->delete(['asset_id' => $assetId]);

        foreach ($testHistories as $testHistory) {
            $jobID = null;
            if (Arr::get($testHistory, 'Job.ID')) {
                $job = $this->jobService->firstOrCreateBySimpro($companyId, Arr::get($testHistory, 'Job.ID'));
                $jobID = $job['id'];
            }

            $testRecord = $this->repository->create([
                'asset_id' => $assetId,
                'job_id' => $jobID,
                'name' => Arr::get($testHistory, 'TestRecord.Employee.Name'),
                'test_date' => Arr::get($testHistory, 'TestRecord.Date'),
                'notes' => Arr::get($testHistory, 'TestRecord.Notes'),
                'result' => Arr::get($testHistory, 'TestRecord.Result')
            ]);

            foreach ($testHistory['TestReadings'] as $testReading) {
                $this->assetTestRecordReadingService->create([
                    'asset_test_record_id' => $testRecord['id'],
                    'name' => $testReading['Name'],
                    'value' => $testReading['Value'],
                ]);
            }
        }
    }
}
