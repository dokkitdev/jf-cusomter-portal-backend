<?php

namespace App\Modules\Notify\Services;

use App\Modules\Notify\DB\Models\NotifyReport;
use App\Modules\Notify\DB\Services\NotifyReportService;
use App\Modules\Notify\Jobs\WarehouseReportJob;

class WarehouseReportService
{
    protected NotifyReportService $notifyReportService;

    public function __construct()
    {
        $this->notifyReportService = app(NotifyReportService::class);
    }

    public function initReportGeneration(int $daysCount): void
    {
        $warehouseProjectReport = $this->notifyReportService->create([
            'report_type' => NotifyReport::REPORT_TYPE_WAREHOUSE_PROJECT,
            'letters_generated' => 0,
            'is_finished' => false,
        ]);

        $warehouseServiceReport = $this->notifyReportService->create([
            'report_type' => NotifyReport::REPORT_TYPE_WAREHOUSE_SERVICE,
            'letters_generated' => 0,
            'is_finished' => false,
        ]);

        dispatch(new WarehouseReportJob($daysCount, $warehouseProjectReport->id, $warehouseServiceReport->id));
    }
}
