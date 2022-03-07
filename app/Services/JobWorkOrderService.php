<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Repositories\JobWorkOrderRepository;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;

/**
 * @property JobWorkOrderRepository $repository
 * @mixin JobWorkOrderRepository
 */
class JobWorkOrderService extends EntityService
{
    protected SimproApiClient $simproClient;

    public function __construct()
    {
        $this->setRepository(JobWorkOrderRepository::class);

        $this->simproClient = app(SimproApiClient::class);
    }

    public function syncBySimpro(int $companyId, array $simproJob, int $jobId): void
    {
        $simproJobWorkOrders = $this->getWorkOrdersFromSimpro($companyId, $simproJob, $jobId);

        $jobWorkOrders = $this->repository->get(['job_id' => $jobId]);

        foreach ($simproJobWorkOrders as $data) {
            $sectionId = $data['simpro_section_id'];
            $costCenterId = $data['simpro_cost_center_id'];
            $workOrderId = $data['simpro_work_order_id'];
            $workOrder = $jobWorkOrders->first(function($item) use ($sectionId, $costCenterId, $workOrderId) {
                return ($item['simpro_section_id'] === $sectionId) && ($item['simpro_cost_center_id'] === $costCenterId) && ($item['simpro_work_order_id'] === $workOrderId);
            });
            if ($workOrder) {
                $this->repository->update($workOrder['id'], $data);
                $jobWorkOrders = $jobWorkOrders->where('id', '!=', $workOrder['id']);
            } else {
                $this->repository->create($data);
            }
        }

        if ($jobWorkOrders->isNotEmpty()) {
            $ids = $jobWorkOrders->pluck('id')->toArray();
            $this->repository->deleteByList($ids);
        }
    }

    protected function getWorkOrdersFromSimpro(int $companyId, array $simproJob, int $jobId): array
    {
        $sections = Arr::get($simproJob, 'Sections', []);
        $allWorkOrders = [];

        foreach ($sections as $section) {
            $sectionId = $section['ID'];
            $costCenters = Arr::get($section, 'CostCenters', []);
            foreach ($costCenters as $costCenter) {
                $costCenterId = $costCenter['ID'];
                $workOrders = $this->simproClient->getWorkOrders($companyId, $simproJob['ID'], $sectionId, $costCenterId);
                if ($workOrders) {
                    foreach ($workOrders as $workOrder) {
                        $allWorkOrders[] = [
                            'job_id' => $jobId,
                            'simpro_section_id' => $sectionId,
                            'simpro_cost_center_id' => $costCenterId,
                            'simpro_work_order_id' => $workOrder['ID'],
                            'name' => (Arr::get($workOrder, 'Staff.Type') === 'employee') ? Arr::get($workOrder, 'Staff.Name') : 'Other Engineer',
                            'description' => Arr::get($workOrder, 'DescriptionNotes'),
                            'date' => Arr::get($workOrder, 'WorkOrderDate'),
                        ];
                    }
                }
            }
        }

        return $allWorkOrders;
    }
}
