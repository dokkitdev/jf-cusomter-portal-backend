<?php

namespace App\Modules\Notify\Services;

use App\Modules\Notify\ApiClients\NotifySimproApiClient;
use App\Modules\Notify\DB\Models\NotifyCsvReport;
use App\Modules\Notify\DB\Services\NotifyCsvReportService;
use App\Modules\Notify\Jobs\ZeroReportJob;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Storage;

class ZeroReportService
{
    protected NotifyCsvReportService $notifyCsvReportService;
    protected NotifySimproApiClient $notifySimproApiClient;

    public function __construct()
    {
        $this->notifyCsvReportService = app(NotifyCsvReportService::class);
        $this->notifySimproApiClient = app(NotifySimproApiClient::class);
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

    public function generateReport(CarbonImmutable $dateFrom, CarbonImmutable $dateTo, int $csvReportId): void
    {
        $tmpFile = tmpfile();

        $this->parseJobs($tmpFile, $dateFrom, $dateTo);

        $storage = Storage::disk('csv_reports');
        $storageFileName = $this->generateStorageFileName($csvReportId);

        $storage->delete($storageFileName);
        $storage->writeStream($storageFileName, $tmpFile);

        $this->notifyCsvReportService->update($csvReportId, [
            'is_finished' => true,
        ]);
    }

    public function failedReportGeneration(int $csvReportId): void
    {
        $this->notifyCsvReportService->delete($csvReportId);
    }

    /**
     * @param resource $csvFile
     */
    protected function parseJobs($csvFile, CarbonImmutable $dateFrom, CarbonImmutable $dateTo): void
    {
        fputcsv($csvFile, __('notify::zero_reports.csv_headers'));

        $jobs = $this->notifySimproApiClient->getJobsIssuedBetweenDates($dateFrom, $dateTo, config('notify.zero_reports.job_customer_id'));

        foreach ($jobs as $job) {
            if ($job['Total']['ExTax'] != 0) {
                continue;
            }

            fputcsv($csvFile, [
                $job['OrderNo'],
                $job['ID'],
                $job['DateIssued'],
                $job['Stage'] === NotifySimproApiClient::JOB_STAGE_ARCHIVED ? '' : $job['Stage'],
                $job['Status']['Name'],
                $job['Total']['ExTax'],
            ]);
        }
    }

    protected function generateFileName(): string
    {
        $now = CarbonImmutable::now();

        return "CHL_ZeroValueJobs_{$now->format('YmdHi')}.csv";
    }

    protected function generateStorageFileName(int $csvReportId): string
    {
        return "{$csvReportId}.csv";
    }
}
