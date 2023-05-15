<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Services\CustomerService;

class SyncCustomers extends AbstractTimeoutCommand
{
    protected $signature = 'simpro:sync-customers';

    protected $description = 'Sync Simpro Customers';

    public function handle(): void
    {
        app(CustomerService::class)->syncCustomers();

        $this->line('Simpro Customers synced');
    }
}
