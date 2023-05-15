<?php

namespace App\Console\Commands\Simpro;

use App\Console\Commands\AbstractTimeoutCommand;
use App\Services\SimproJobService;

class DeleteErrorJobsHandler extends AbstractTimeoutCommand
{
    protected $signature = 'simpro:delete-error-jobs';

    protected $description = 'Delete Simpro error jobs';

    public function handle(): void
    {
        $date = now()->subMonths(1);

        app(SimproJobService::class)->deleteErrorJobs($date);

        $this->line('Error Simpro Jobs deleted');
    }
}
