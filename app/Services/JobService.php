<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\SimproJob;
use App\Repositories\JobRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property JobRepository $repository
 * @mixin JobRepository
 */
class JobService extends BaseService
{
    protected SimproApiClient $simproClient;
    protected SettingService $settingService;
    protected int $companyId;
    protected SiteService $siteService;
    protected CustomerService $customerService;
    protected JobCatalogService $jobCatalogService;
    protected JobAttachmentService $jobAttachmentService;
    protected JobWorkOrderService $jobWorkOrderService;

    public function __construct()
    {
        parent::__construct();

        $this->setRepository(JobRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->settingService = app(SettingService::class);
        $this->companyId = config('services.simpro.company_id');
        $this->siteService = app(SiteService::class);
        $this->customerService = app(CustomerService::class);
        $this->jobCatalogService = app(JobCatalogService::class);
        $this->jobAttachmentService = app(JobAttachmentService::class);
        $this->jobWorkOrderService = app(JobWorkOrderService::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::CUSTOMER) {
            $filters['job_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->with(Arr::get($filters, 'with', []))
            ->searchQuery($filters)
            ->filterByIntQuery('simpro_job_id')
            ->filterBy('customer_id')
            ->filterBy('site_id')
            ->filterBy('customer.name', 'customer_name')
            ->filterBy('site.name', 'site_name')
            ->filterByPostalCode()
            ->filterByList('cost_center_name', 'cost_center_name')
            ->filterByList('stage', 'stage')
            ->filterByList('job_status', 'job_status')
            ->filterByRecentSchedule()
            ->filterByQuery(['site.name', 'site.postal_code', 'customer.name'])
            ->filterByOnlyPermitted()
            ->getSearchResults();
    }

    public function createInSimpro(array $data): array
    {
        $simproSite = $this->siteService->with(['customer'])->find($data['site_id']);

        $defaultTag = $this->settingService->get('default_tag');

        $jobStatus = config('defaults.job_status');

        $jobData = [
            'Type' => 'Project',
            'Customer' => Arr::get($simproSite, 'simpro_customer.customer_id'),
            'Site' => $simproSite['site_id'],
            'Tags' => [$defaultTag['ID']],
            'Status' => $jobStatus
        ];

        if (Arr::has($data, 'description')) {
            $jobData['Description'] = $data['description'];
        }

        $job = $this->simproClient->postJob($this->companyId, $jobData);

        if (Arr::has($data, 'files')) {
            foreach ($data['files'] as $file) {
                $this->simproClient->postJobAttachment($this->companyId, $job['ID'], [
                    'Filename' => $file['filename'],
                    'Base64Data' => base64_encode($file['content']),
                    'Public' => true
                ]);
            }
        }

        return $job;
    }

    public function createOrUpdateBySimpro(SimproJob $webhook): Model
    {
        $companyId = $webhook['data']['reference']['companyID'];
        $simproJobId = $webhook['data']['reference']['jobID'];

        $simproJob = $this->simproClient->getJob($companyId, $simproJobId);

        $customer = $this->customerService->firstOrCreateBySimpro($companyId, $simproJob['Customer']['ID']);

        $site = $this->siteService->firstOrCreateBySimpro($companyId, $simproJob['Site']['ID']);

        $job = $this->createOrUpdate($companyId, $simproJob, $customer['id'], $site['id']);

        app(ScheduleService::class)->createOrUpdateManyBySimpro($companyId, $simproJobId, $job['id']);

        $this->jobCatalogService->syncBySimpro($simproJob, $job['id']);

        $this->jobAttachmentService->syncBySimpro($companyId, $simproJobId, $job['id']);

        $this->jobWorkOrderService->syncBySimpro($companyId, $simproJob, $job['id']);

        return $job;
    }

    public function firstOrCreateBySimpro(int $companyId, int $simproJobId): Model
    {
        $job = $this->repository->findBy('simpro_job_id', $simproJobId);

        if ($job) {
            return $job;
        }

        $simproJob = $this->simproClient->getJob($companyId, $simproJobId);

        $customer = $this->customerService->getOrCreateBySimpro($companyId, $simproJob['Customer']);

        $site = $this->siteService->getOrCreateBySimpro($companyId, $simproJob['Site']['ID'], $customer['id']);

        $job = $this->createOrUpdate($companyId, $simproJob, $customer['id'], $site['id']);

        $this->jobAttachmentService->syncBySimpro($companyId, $simproJobId, $job['id']);

        return $job;
    }

    protected function createOrUpdate(int $companyId, array $simproJob, int $customerId, int $siteId): Model
    {
        $jobLog = $this->simproClient->getJobLog($companyId, $simproJob['ID']);

        return $this->repository->updateOrCreate(['simpro_job_id' => $simproJob['ID']], [
            'customer_id' => $customerId,
            'site_id' => $siteId,
            'order_no' => Arr::get($simproJob, 'OrderNo'),
            'description' => Arr::get($simproJob, 'Description'),
            'priority' => Arr::get($simproJob, 'ResponseTime.Name'),
            'cost_center_name' => Arr::get($simproJob, 'Sections.0.CostCenters.0.CostCenter.Name'),
            'stage' => Arr::get($simproJob, 'Stage'),
            'job_status' => Arr::get($simproJob, 'Status.Name'),
            'date_created' => Arr::get($simproJob, 'DateIssued'),
            'made_safe_date' => Arr::get($jobLog, '0.DateLogged'),
            'completion_date' => Arr::get($simproJob, 'CompletedDate'),
            'due_date' => Arr::get($simproJob, 'DueDate'),
        ]);
    }

    public function deleteBySimpro(SimproJob $webhook): int
    {
        $simproJobId = $webhook['data']['reference']['jobID'];

        return $this->repository->delete(['simpro_job_id' => $simproJobId]);
    }
}
