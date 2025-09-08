<?php

namespace App\Modules\Notify\Jobs;

use App\Jobs\AbstractJob;
use App\Modules\Notify\Services\AssetReportService;

class AssetReportJob extends AbstractJob
{
    protected int $csvReportId;

    public function __construct(int $csvReportId)
    {
        $this->csvReportId = $csvReportId;

        $this->onQueue('notify:asset_report');
    }

    public function handle()
    {
        app(AssetReportService::class)->generateReport($this->csvReportId);
    }

    public function failed(): void
    {
        app(AssetReportService::class)->failedReportGeneration($this->csvReportId);
    }

    public function getCsvReportId(): int
    {
        return $this->csvReportId;
    }
}
