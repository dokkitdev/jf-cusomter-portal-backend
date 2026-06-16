<?php

namespace App\Console\Commands\FullUpdateSimpro;

use App\ApiClients\SimproApiClient;
use App\Console\Commands\AbstractTimeoutCommand;
use App\Services\SimproJobService;
use Carbon\Carbon;

class FullUpdateJobsCommand extends AbstractTimeoutCommand
{
    protected $signature = 'update-simpro:jobs';

    protected $description = 'Update Simpro jobs';

    protected SimproJobService $simproJobService;

    public function handle(): void
    {
        $this->processJob();

    }

    public function processJob($page = 1)
    {
        $simproClient = app(SimproApiClient::class);
        $jobs = $simproClient->getJobs(0, [
            'columns' => 'ID,Stage,Status,Description,Name,Sections.CostCenters,Site,Customer,OrderNo,ResponseTime,Sections,DateModified',
            'pageSize' => 250,
            'page' => 1,
            'orderbydesc' => 'DateModified',
        ]);

        foreach ($jobs as $job) {

            $input = Carbon::parse($job['DateModified']);

            $startOfCurrentMonth = Carbon::now()->startOfMonth();

            $isOlderThanCurrentMonth = $input->lt($startOfCurrentMonth);
           dump($job['ID']);
           dump($isOlderThanCurrentMonth);
        }
        if (count($jobs) < 250) {
            return;
        }

        $this->processJob($page + 1);
    }
}
