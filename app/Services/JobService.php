<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\Role;
use App\Models\SimproJob;
use App\Repositories\JobRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

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
    protected JobNoAccessDateService $jobNoAccessDateService;

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
        $this->jobNoAccessDateService = app(JobNoAccessDateService::class);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $authUser = $this->getAuthUser();

        if ($authUser['role_id'] === Role::CUSTOMER) {
            $filters['job_has_user'] = $authUser['id'];
        }

        return $this->repository
            ->with(Arr::get($filters, 'with', []))
            ->withCount(Arr::get($filters, 'with_count', []))
            ->searchQuery($filters)
            ->filterByIntQuery('simpro_job_id')
            ->filterBy('customer_id')
            ->filterBy('site_id')
            ->filterBy('customer.name', 'customer_name')
            ->filterByPostalCode()
            ->filterByPriority()
            ->filterByOrderNo()
            ->filterByUprn()
            ->filterBySiteName()
            ->filterByList('cost_center_name', 'cost_center_name')
            ->filterByList('stage', 'stage')
            ->filterByList('job_status', 'job_status')
            ->filterByRecentSchedule()
            ->filterByQuery(['site.name', 'site.postal_code', 'customer.name'])
            ->filterBy('date_created')
            ->filterFrom('date_created', false, 'date_created_from')
            ->filterTo('date_created', false, 'date_created_to')
            ->filterFrom('made_safe_date', false, 'made_safe_date_from')
            ->filterTo('made_safe_date', false, 'made_safe_date_to')
            ->filterFrom('completion_date', false, 'completion_date_from')
            ->filterTo('completion_date', false, 'completion_date_to')
            ->filterFrom('due_date', false, 'due_date_from')
            ->filterTo('due_date', false, 'due_date_to')
            ->filterByOutOfHours()
            ->filterByOnlyPermitted()
            ->getSearchResults();
    }

    public function getCostCenters()
    {
        $costCenterPages = $this->simproClient->getCostCenters($this->companyId);

        $costCenters = [];
        foreach ($costCenterPages as $costCenterPage) {
            $costCenters = array_merge($costCenters, $costCenterPage);
        }

        return $costCenters;
    }

    public function getStatuses()
    {
        $jobStatusPages = $this->simproClient->getJobStatuses($this->companyId);

        $jobStatuses = [];
        foreach ($jobStatusPages as $jobStatusPage) {
            $jobStatuses = array_merge($jobStatuses, $jobStatusPage);
        }

        return $jobStatuses;
    }

    public function createInSimpro(array $data): array
    {
        $site = $this->siteService->with(['customer'])->find($data['site_id']);

        $tag = $this->getAuthUser()->role_id === Role::CUSTOMER ? config('defaults.customer_job_request_tag') : config('defaults.call_center_job_request_tag') ;

        $jobData = [
            'Type' => 'Service',
            'Customer' => Arr::get($site, 'customer.simpro_customer_id'),
            'Site' => $site['simpro_site_id'],
            'Tags' => [$tag],
            'DueDate' => now()->addDays(3)->format('Y-m-d')
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

        $customer = $this->customerService->firstOrCreateBySimpro($companyId, $simproJob['Customer']['ID']);

        $site = $this->siteService->firstOrCreateBySimpro($companyId, $simproJob['Site']['ID']);

        $job = $this->createOrUpdate($companyId, $simproJob, $customer['id'], $site['id']);

        $this->jobAttachmentService->syncBySimpro($companyId, $simproJobId, $job['id']);

        return $job;
    }

    protected function createOrUpdate(int $companyId, array $simproJob, int $customerId, int $siteId): Model
    {
        $madeSafeJobLog = $this->simproClient->getMadeSafeJobLog($companyId, $simproJob['ID']);
        $createdJobLog = $this->simproClient->getCreatedJobLog($companyId, $simproJob['ID']);
        $completedJobLog = $this->simproClient->getCompletedJobLog($companyId, $simproJob['ID']);

        $calculatedDueDate = null;

        if (Arr::get($createdJobLog, '0.DateLogged') && Arr::get($simproJob, 'ResponseTime')) {
            $loggedCreateDate = Carbon::parse(Arr::get($createdJobLog, '0.DateLogged'));

            $calculatedDueDate = $loggedCreateDate
                ->addDays(Arr::get($simproJob, 'ResponseTime.Days', 0))
                ->addHours(Arr::get($simproJob, 'ResponseTime.Hours', 0))
                ->addMinutes(Arr::get($simproJob, 'ResponseTime.Minutes', 0));
        }

        $job = $this->repository->updateOrCreate(['simpro_job_id' => $simproJob['ID']], [
            'customer_id' => $customerId,
            'site_id' => $siteId,
            'order_no' => Arr::get($simproJob, 'OrderNo'),
            'description' => Arr::get($simproJob, 'Description'),
            'priority' => Arr::get($simproJob, 'ResponseTime.Name'),
            'cost_center_name' => Arr::get($simproJob, 'Sections.0.CostCenters.0.CostCenter.Name'),
            'stage' => Arr::get($simproJob, 'Stage'),
            'job_status' => Arr::get($simproJob, 'Status.Name'),
            'date_created' => Arr::get($simproJob, 'DateIssued'),
            'made_safe_date' => Arr::get($madeSafeJobLog, '0.DateLogged'),
            'completion_date' => Arr::get($simproJob, 'CompletedDate'),
            'due_date' => $calculatedDueDate ? $calculatedDueDate->format('Y-m-d H:i:s') : Arr::get($simproJob, 'DueDate'),
            'logged_create_date' => Arr::get($createdJobLog, '0.DateLogged', Arr::get($simproJob, 'DateIssued')),
            'logged_completion_date' => Arr::get($completedJobLog, '0.DateLogged'),
        ]);

        $this->jobNoAccessDateService->syncBySimpro($companyId, $simproJob['ID'], $job['id']);

        app(ScheduleService::class)->createOrUpdateManyBySimpro($companyId, $simproJob['ID'], $job['id']);

        return $job;
    }

    public function deleteBySimpro(SimproJob $webhook): int
    {
        $simproJobId = $webhook['data']['reference']['jobID'];

        return $this->repository->delete(['simpro_job_id' => $simproJobId]);
    }
}
