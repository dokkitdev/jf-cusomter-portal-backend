<?php

namespace App\Services;

use App\Models\PrivateContractCostCenterItem;
use Illuminate\Support\Arr;
use RonasIT\Support\Services\EntityService;
use App\Repositories\PrivateContractCostCenterItemRepository;

/**
 * @mixin PrivateContractCostCenterItemRepository
 * @property PrivateContractCostCenterItemRepository $repository
 */
class PrivateContractCostCenterItemService extends EntityService
{
    public function __construct()
    {
        $this->setRepository(PrivateContractCostCenterItemRepository::class);
    }

    public function createByCostCenter(
        int $costCenterId,
        array $simproCostCenterData
    ): void {
        foreach ($simproCostCenterData['Items'] as $itemType => $items) {
            if (empty($itemData) && in_array($itemType, PrivateContractCostCenterItem::TYPES_FROM_SIMPRO)) {
                $this->createItems($costCenterId, $items, $itemType);
            }
        }

        if ($exTax = Arr::get($simproCostCenterData, 'Totals.Discount', 0)) {
            $this->createDiscountItem($costCenterId, $exTax);
        }
    }

    protected function createItems(
        int $privateContractCostCenterId,
        array $items,
        string $type
    ): void {
        foreach ($items as $item) {
            $name = Arr::get($item, config("defaults.private_contract_cost_center_item.{$type}.key_name"));

            $this->create([
                'private_contract_cost_center_id' => $privateContractCostCenterId,
                'simpro_recurring_invoice_cost_center_item_id' => $item['ID'],
                'qty' => Arr::get($item, 'Total.Qty'),
                'ex_tax' => Arr::get($item, 'Total.Amount.ExTax'),
                'inc_tax' => Arr::get($item, 'Total.Amount.IncTax'),
                'order' => config("defaults.private_contract_cost_center_item.{$type}.order"),
                'type' => $type,
                'name' => $name,
            ]);
        }
    }

    protected function createDiscountItem(int $costCenterId, float $exTax): void
    {
        $type = PrivateContractCostCenterItem::TYPE_DISCOUNT;
        $incTaxCoef = config("defaults.private_contract_cost_center_item.{$type}.inc_tax_coef");
        $incTax = $exTax + round($exTax * $incTaxCoef, 2);

        $this->create([
            'private_contract_cost_center_id' => $costCenterId,
            'order' => config("defaults.private_contract_cost_center_item.{$type}.order"),
            'ex_tax' => $exTax,
            'inc_tax' => $incTax,
            'type' => $type,
            'name' => $type,
        ]);
    }
}
