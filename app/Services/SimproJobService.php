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

    public function __construct()
    {
        $this->setRepository(SimproJobRepository::class);

        $this->customerService = app(CustomerService::class);
        $this->siteService = app(SiteService::class);
        $this->jobService = app(JobService::class);
    }

    public function handleJob(SimproJob $webhook)
    {
        $event = $webhook['data']['ID'];

        switch ($event) {
            case 'job.created':
            case 'job.updated':
                return $this->jobService->createOrUpdateBySimpro($webhook);
            case 'job.deleted':
                return $this->jobService->deleteBySimpro($webhook);
            case 'site.created':
            case 'site.updated':
                return $this->siteService->createOrUpdateBySimpro($webhook);
            case 'site.deleted':
                return $this->siteService->deleteBySimpro($webhook);
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
