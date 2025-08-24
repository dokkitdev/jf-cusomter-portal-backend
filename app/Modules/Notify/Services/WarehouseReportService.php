<?php

namespace App\Modules\Notify\Services;

use App\Modules\Notify\DB\Models\NotifyReport;
use App\Modules\Notify\DB\Services\NotifyReportService;
use App\Modules\Notify\Jobs\WarehouseReportJob;
use Illuminate\Support\Carbon;

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

        $job = new WarehouseReportJob($daysCount, $warehouseProjectReport->id, $warehouseServiceReport->id);

        //TODO: remove after report generation is implemented
        $job->delay(Carbon::now()->addMinute());

        dispatch($job);
    }

    public function generateReport(int $daysCount, int $warehouseProjectReportId, int $warehouseServiceReportId): void
    {
        $this->notifyReportService->update($warehouseProjectReportId, [
            'is_finished' => true,
        ]);

        $this->notifyReportService->update($warehouseServiceReportId, [
            'is_finished' => true,
        ]);
    }

    public function failedReportGeneration(int $warehouseProjectReportId, int $warehouseServiceReportId): void
    {
        $this->notifyReportService->delete($warehouseProjectReportId);
        $this->notifyReportService->delete($warehouseServiceReportId);
    }
}
