<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\SimproJob;
use App\Models\SimproLog;
use App\Repositories\SimproLogRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RonasIT\Support\Services\EntityService;

/**
 * @property SimproLogRepository $repository
 * @mixin SimproLogRepository
 */
class SimproLogService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected int $companyId;
    protected SiteService $siteService;
    protected JobService $jobService;
    protected AssetService $assetService;
    protected AssetLogHistoryService $assetLogHistoryService;

    public function __construct()
    {
        $this->setRepository(SimproLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->siteService = app(SiteService::class);
        $this->jobService = app(JobService::class);
        $this->assetService = app(AssetService::class);
        $this->assetLogHistoryService = app(AssetLogHistoryService::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('loggable_type')
            ->filterBy('handle_status')
            ->filterByMod()
            ->getSearchResults();
    }

    public function saveAll(string $loggableType): void
    {
        if ($loggableType === SimproLog::LOGGABLE_TYPE_ASSETS) {
            $this->saveAllAssets($loggableType);
        } else {
            $pages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/{$loggableType}/");

            foreach ($pages as $page) {
                foreach ($page as $item) {
                    $this->repository->updateOrCreate([
                        'loggable_id' => $item['ID'],
                        'loggable_type' => $loggableType
                    ], []);
                }
            }
        }
    }

    public function handle(string $loggableType, ?string $divider = null, ?string $mod = null): void
    {
        $simproLogs = $this->search([
            'loggable_type' => $loggableType,
            'handle_status' => SimproLog::HANDLE_STATUS_NEW,
            'page' => 1,
            'per_page' => 1000,
            'order_by' => 'id',
            'divider' => $divider,
            'mod' => $mod
        ]);

        if ($simproLogs->isNotEmpty()) {
            $simproLogs->getCollection()->each(function ($simproLog) use ($loggableType) {
                try {
                    switch ($loggableType) {
                        case SimproLog::LOGGABLE_TYPE_SITES:
                            $this->handleSite($simproLog);
                            break;
                        case SimproLog::LOGGABLE_TYPE_JOBS:
                            $this->handleJob($simproLog);
                            break;
                        case SimproLog::LOGGABLE_TYPE_ASSETS:
                            $this->handleAsset($simproLog);
                            break;
                    }

                    $this->delete($simproLog['id']);
                } catch (Exception $e) {
                    report($e);

                    $this->update($simproLog['id'], [
                        'handle_status' => SimproLog::HANDLE_STATUS_ERROR,
                        'handle_result' => [
                            'code' => $e->getCode(),
                            'message' => $e->getMessage()
                        ]
                    ]);
                }
            });
        }
    }

    protected function handleSite(SimproLog $simproLog): void
    {
        $simproJob = new SimproJob([
            'data' => [
                'reference' => [
                    'companyID' => 0,
                    'siteID' => $simproLog['loggable_id']
                ]
            ]
        ]);

        $this->siteService->createOrUpdateBySimpro($simproJob);
    }

    protected function handleJob(SimproLog $simproLog): void
    {
        $simproJob = new SimproJob([
            'data' => [
                'reference' => [
                    'companyID' => 0,
                    'jobID' => $simproLog['loggable_id']
                ]
            ]
        ]);

        $this->jobService->createOrUpdateBySimpro($simproJob);
    }

    protected function handleAsset(SimproLog $simproLog): void
    {
        $simproJob = new SimproJob([
            'data' => [
                'reference' => [
                    'companyID' => 0,
                ],
                'description' => "{$simproLog['loggable_id']}"
            ]
        ]);

        $this->assetService->updateOrCreateBySimpro($simproJob);
    }

    protected function saveAllAssets(string $loggableType)
    {
        $startDate = now()->subMinutes(30);

        $assetLogHistory = $this->assetLogHistoryService->last();

        $headers = [];

        if ($assetLogHistory) {
            $headers['If-Modified-Since'] = Carbon::createFromFormat('Y-m-d H:i:s', $assetLogHistory['assets_pulled_at'])->toRfc7231String();
        }

        $assetsPages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/customerAssets/", [], 250, $headers);

        $assetsCount = 0;

        foreach ($assetsPages as $assetsPage) {
            $assetsCount += count($assetsPage);

            foreach ($assetsPage as $simproAsset) {
                $this->repository->updateOrCreate([
                    'loggable_id' => $simproAsset['ID'],
                    'loggable_type' => $loggableType
                ], []);
            }
        }

        $this->assetLogHistoryService->create([
            'assets_pulled_at' => $startDate,
            'assets_count' => $assetsCount
        ]);
    }
}
