<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\SimproJob;
use App\Models\SimproLog;
use App\Repositories\SimproLogRepository;
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

    public function __construct()
    {
        $this->setRepository(SimproLogRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->siteService = app(SiteService::class);

        $this->companyId = config('services.simpro.company_id');
    }

    public function search(array $filters): LengthAwarePaginator
    {
        return $this->repository
            ->searchQuery($filters)
            ->filterBy('loggable_type')
            ->filterBy('handle_status')
            ->getSearchResults();
    }

    public function saveAll(string $loggableType): void
    {
        $sitePages = $this->simproClient->getAsGenerator("companies/{$this->companyId}/{$loggableType}/");

        foreach ($sitePages as $sitePage) {
            foreach ($sitePage as $simproSite) {
                $this->repository->updateOrCreate([
                    'loggable_id' => $simproSite['ID'],
                    'loggable_type' => $loggableType
                ], []);
            }
        }
    }

    public function handle(string $loggableType): void
    {
        $simproLogs = $this->search([
            'loggable_type' => $loggableType,
            'handle_status' => SimproLog::HANDLE_STATUS_NEW,
            'page' => 1,
            'per_page' => 1000,
            'order_by' => 'id',
        ]);

        if ($simproLogs->isNotEmpty()) {
            $simproLogs->getCollection()->each(function ($simproLog) use ($loggableType) {
                try {
                    switch ($loggableType) {
                        case SimproLog::LOGGABLE_TYPE_SITES:
                            $this->handleSite($simproLog);
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

    protected function handleSite($simproLog): void
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
}
