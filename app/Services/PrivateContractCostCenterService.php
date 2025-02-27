<?php

namespace App\Services;

use App\ApiClients\SimproApiClient;
use App\Models\PrivateContract;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;
use App\Repositories\PrivateContractCostCenterRepository;

/**
 * @mixin PrivateContractCostCenterRepository
 * @property PrivateContractCostCenterRepository $repository
 */
class PrivateContractCostCenterService extends EntityService
{
    protected SimproApiClient $simproClient;
    protected PrivateContractCostCenterItemService $privateContractCostCenterItemService;
    protected int $companyId;

    public function __construct()
    {
        $this->setRepository(PrivateContractCostCenterRepository::class);

        $this->simproClient = app(SimproApiClient::class);
        $this->privateContractCostCenterItemService = app(PrivateContractCostCenterItemService::class);
        $this->companyId = config('services.simpro.company_id');
    }

    public function createByPrivateContract(PrivateContract $privateContract): void
    {
        $costCentersPages = $this->simproClient->getRecurringInvoiceCostCenters($this->companyId, $privateContract->simpro_recurring_invoice_id);

        foreach ($costCentersPages as $page) {
            foreach ($page as $costCenter) {
                $costCenterData = $this->simproClient->getRecurringInvoiceCostCenter(
                    $this->companyId,
                    $privateContract->simpro_recurring_invoice_id,
                    Arr::get($costCenter, 'Section.ID'),
                    $costCenter['ID'],
                );

                $this->processCostCenter(
                    $costCenter['Section'],
                    $costCenterData,
                    $privateContract,
                );
            }
        }
    }

    protected function processCostCenter(array $sectionData, array $costCenterData, PrivateContract $privateContract): void
    {
        $costCenter = $this->create([
            'name' => Arr::get($costCenterData, 'CostCenter.Name'),
            'private_contract_id' => $privateContract->id,
            'simpro_cost_center_id' => $costCenterData['ID'],
            'ex_tax' => Arr::get($costCenterData, 'Total.ExTax'),
            'tax' => Arr::get($costCenterData, 'Total.Tax'),
            'inc_tax' => Arr::get($costCenterData, 'Total.IncTax'),
            'simpro_section_id' => $sectionData['ID'],
            'section_name' => $sectionData['Name'],
        ]);

        $this->privateContractCostCenterItemService->createByCostCenter($costCenter->id, $costCenterData);
    }
}
