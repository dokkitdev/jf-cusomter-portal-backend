<?php

namespace App\Modules\Notify\Jobs;

use App\Jobs\AbstractJob;

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
