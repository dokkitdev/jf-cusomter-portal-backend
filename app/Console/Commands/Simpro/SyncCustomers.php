<?php

namespace App\Console\Commands\Simpro;

use App\Services\CustomerService;
use Illuminate\Console\Command;

class SyncCustomers extends Command
{
    protected $signature = 'simpro:sync-customers';

    protected $description = 'Sync Simpro Customers';

    public function handle()
    {
        app(CustomerService::class)->syncCustomers();

        $this->line('Simpro Customers synced');
    }
}
