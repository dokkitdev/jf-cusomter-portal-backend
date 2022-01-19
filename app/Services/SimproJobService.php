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

    public function __construct()
    {
        $this->setRepository(SimproJobRepository::class);

        $this->customerService = app(CustomerService::class);
    }

    public function handleJob(SimproJob $webhook)
    {
        $event = $webhook['data']['ID'];

        switch ($event) {
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
