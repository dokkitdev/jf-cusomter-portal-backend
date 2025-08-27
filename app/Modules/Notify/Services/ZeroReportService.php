<?php

namespace App\Modules\Notify\Services;

use App\Modules\Notify\DB\Models\NotifyCsvReport;
use App\Modules\Notify\DB\Services\NotifyCsvReportService;
use App\Modules\Notify\Jobs\ZeroReportJob;
use Carbon\CarbonImmutable;

class ZeroReportService
{
    protected NotifyCsvReportService $notifyCsvReportService;

    public function __construct()
    {
        $this->notifyCsvReportService = app(NotifyCsvReportService::class);
    }

    public function initReportGeneration(CarbonImmutable $dateFrom, CarbonImmutable $dateTo): void
    {
        /** @var NotifyCsvReport $csvReport */
        $csvReport = $this->notifyCsvReportService->create([
            'report_type' => NotifyCsvReport::REPORT_TYPE_ZERO,
            'file_name' => $this->generateFileName(),
            'is_finished' => false,
        ]);

        dispatch(new ZeroReportJob($dateFrom, $dateTo, $csvReport->id));
    }

    protected function generateFileName(): string
    {
        $now = CarbonImmutable::now();

        return "CHL_ZeroValueJobs_{$now->format('YmdHi')}.csv";
    }
}
