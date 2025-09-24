<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SimproJob;
use App\Repositories\SimproJobRepository;
use RonasIT\Support\Services\EntityService;

/**
 * @property SimproJobRepository $repository
 * @mixin SimproJobRepository
 */
class SimproJobService extends EntityService
{
    protected CustomerService $customerService;
    protected SiteService $siteService;
    protected JobService $jobService;
    protected AssetService $assetService;
    protected ScheduleService $scheduleService;

    public function __construct()
    {
        $this->setRepository(SimproJobRepository::class);

        $this->customerService = app(CustomerService::class);
        $this->siteService = app(SiteService::class);
        $this->jobService = app(JobService::class);
        $this->assetService = app(AssetService::class);
        $this->scheduleService = app(ScheduleService::class);
    }

    public function handleJob(SimproJob $webhook)
    {
        $event = $webhook['data']['ID'];

        dump($event);

        switch ($event) {
            case 'job.created':
            case 'job.updated':
            case 'job.status':
            case 'job.stage.complete':
            case 'job.stage.archived':
                return $this->jobService->createOrUpdateBySimpro($webhook);
            case 'job.deleted':
                return $this->jobService->deleteBySimpro($webhook);
            case 'job.schedule.created':
            case 'job.schedule.updated':
                return $this->scheduleService->updateOrCreateBySimpro($webhook);
            case 'job.schedule.deleted':
                return $this->scheduleService->deleteBySimpro($webhook);
            case 'job.asset.tested':
                $this->assetService->processAssetTestedWebhook($webhook);
                break;
            case 'site.created':
            case 'site.updated':
                return $this->siteService->createOrUpdateBySimpro($webhook);
            case 'site.deleted':
                return $this->siteService->deleteBySimpro($webhook);
            case 'asset.created':
            case 'asset.updated':
                return $this->assetService->updateOrCreateBySimpro($webhook);
            case 'asset.deleted':
                return $this->assetService->deleteBySimpro($webhook);
            case 'company.customer.created':
            case 'company.customer.updated':
                return $this->customerService->createOrUpdateBySimpro($webhook, Customer::TYPE_COMPANIES);
            case 'company.customer.deleted':
                return $this->customerService->deleteBySimpro($webhook, Customer::TYPE_COMPANIES);
            case 'individual.customer.created':
            case 'individual.customer.updated':
                return $this->customerService->createOrUpdateBySimpro($webhook, Customer::TYPE_INDIVIDUALS);
            case 'individual.customer.deleted':
                return $this->customerService->deleteBySimpro($webhook, Customer::TYPE_INDIVIDUALS);
            default: return true;
        }
    }
}
