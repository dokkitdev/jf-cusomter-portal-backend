<?php

namespace App\Repositories;

use App\Models\PrivateContract;
use Illuminate\Support\Carbon;
use RonasIT\Support\Repositories\BaseRepository;

/**
 * @property PrivateContract $model
 */
class PrivateContractRepository extends BaseRepository
{
    public function __construct()
    {
        $this->setModel(PrivateContract::class);
    }

    public function deleteNotProcessed(int $customerId, int $simproInvoiceId): void
    {
        $this
            ->getQuery([
                'customer_id' => $customerId,
                'simpro_recurring_invoice_id' => $simproInvoiceId
            ])
            ->where('next_recurring_date', '>=', Carbon::now()->startOfYear())
            ->where('is_processed', false)
            ->delete();

    }

    public function existsForCurrentYear(int $customerId, int $simproInvoiceId): bool
    {
        return $this
            ->getQuery([
                'customer_id' => $customerId,
                'simpro_recurring_invoice_id' => $simproInvoiceId
            ])
            ->whereYear('next_recurring_date', Carbon::now()->year)
            ->exists();
    }
}
