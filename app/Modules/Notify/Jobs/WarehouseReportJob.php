<?php

namespace App\Modules\Notify\Jobs;

use App\Jobs\AbstractJob;
use App\Modules\Notify\Services\WarehouseReportService;

class WarehouseReportJob extends AbstractJob
{
    protected int $daysCount;
    protected int $warehouseProjectReportId;
    protected int $warehouseServiceReportId;

    public function __construct(int $daysCount, int $warehouseProjectReportId, int $warehouseServiceReportId)
    {
        $this->daysCount = $daysCount;
        $this->warehouseProjectReportId = $warehouseProjectReportId;
        $this->warehouseServiceReportId = $warehouseServiceReportId;

        $this->onQueue('notify:warehouse_report');
    }

    public function handle()
    {
        app(WarehouseReportService::class)->generateReport(
            $this->daysCount,
            $this->warehouseProjectReportId,
            $this->warehouseServiceReportId,
        );
    }

    public function failed(): void
    {
        app(WarehouseReportService::class)->failedReportGeneration(
            $this->warehouseProjectReportId,
            $this->warehouseServiceReportId,
        );
    }
    
    public function getDaysCount(): int
    {
        return $this->daysCount;
    }

    public function getWarehouseProjectReportId(): int
    {
        return $this->warehouseProjectReportId;
    }

    public function getWarehouseServiceReportId(): int
    {
        return $this->warehouseServiceReportId;
    }
}
