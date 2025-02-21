<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Services\PrivateContractService;
use Illuminate\Support\Carbon;

class SyncRecurringInvoices extends AbstractTimeoutCommand
{
    protected $signature = 'simpro:sync-recurring-invoices';

    protected $description = 'Sync Reccuring invoices';

    public function handle(): void
    {
        $fromDate = Carbon::now();

        $toDate = Carbon::now()->addDays(30);

        app(PrivateContractService::class)->sync($fromDate, $toDate);

        $this->line('Simpro Reccuring invoices synced!');
    }
}
