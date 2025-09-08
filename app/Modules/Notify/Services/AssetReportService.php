<?php

namespace App\Modules\Notify\Services;

use App\Modules\Notify\DB\Models\NotifyAssetReport;
use App\Modules\Notify\DB\Models\NotifyCsvReport;
use App\Modules\Notify\DB\Services\NotifyAssetReportService;
use App\Modules\Notify\DB\Services\NotifyCsvReportService;
use App\Modules\Notify\Jobs\AssetReportJob;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class AssetReportService
{
    protected const CSV_HEADER = [
        'Site ID', '~UPRN', 'AssetID', 'AssetType', 'Type', 'Fuel Type', 'Make', 'Model', 'Location',
        'Last Service Date', 'Service Level Start Date', 'Job Due Date', 'Service Level Next Service Date',
        'Job Stage', 'Service Level Name', 'Last MOT Date', 'Service Due', 'Next Scheduled Appointment Date',
        'No Access Visits', 'Cancellations',
    ];

    protected const JOB_DUE_DATE_JOB_STAGES = [
        'Progress', 'Pending',
    ];

    protected const JOB_STAGE_ARCHIVED = 'Archived';

    protected NotifyAssetReportService $notifyAssetReportService;
    protected NotifyCsvReportService $notifyCsvReportService;

    public function __construct()
    {
        $this->notifyAssetReportService = app(NotifyAssetReportService::class);
        $this->notifyCsvReportService = app(NotifyCsvReportService::class);
    }

    public function initReportGeneration(): void
    {
        /** @var NotifyCsvReport $csvReport */
        $csvReport = $this->notifyCsvReportService->create([
            'report_type' => NotifyCsvReport::REPORT_TYPE_ASSET,
            'file_name' => $this->generateFileName(),
            'is_finished' => false,
        ]);

        dispatch(new AssetReportJob($csvReport->id));
    }

    public function generateReport(int $csvReportId): void
    {
        $tmpFile = tmpfile();

        $this->prepareCsv($tmpFile);

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

    protected function prepareCsv($csvFile): void
    {
        $now = Carbon::now()->format('Y-m-d');
        $today = Carbon::now()->addDay()->format('Y-m-d');

        fputcsv($csvFile, self::CSV_HEADER);

        /** @var NotifyAssetReport[] $assetReportRows */
        $assetReportRows = $this->notifyAssetReportService->iterateAll();

        foreach ($assetReportRows as $assetReportRow) {
            $formattedJobDueDate = isset($assetReportRow->job_due_date) && in_array($assetReportRow->job_stage, self::JOB_DUE_DATE_JOB_STAGES)
                ? $assetReportRow->job_due_date->format('Y-m-d')
                : '';

            $cancellation = ($assetReportRow->job_stage !== self::JOB_STAGE_ARCHIVED) && isset($assetReportRow->cancellation)
                ? $assetReportRow->cancellation
                : '';

            $nextScheduledAppointmentDate = '';
            if (isset($assetReportRow->next_scheduled_appointment_date)) {
                $calendarDate = Arr::first(explode(' ', $assetReportRow->next_scheduled_appointment_date));

                if ($calendarDate >= $now) {
                    $nextScheduledAppointmentDate = $assetReportRow->next_scheduled_appointment_date;
                }
            }

            $formattedServiceDue = (empty($formattedJobDueDate) || ($formattedJobDueDate < $today))
                ? (isset($assetReportRow->next_service_date) ? $assetReportRow->next_service_date->format('Y-m-d') : '')
                : $formattedJobDueDate;

            fputcsv($csvFile, [
                $assetReportRow->site_id,
                $assetReportRow->uprn,
                $assetReportRow->asset_id,
                $assetReportRow->asset_type,
                $assetReportRow->type,
                $assetReportRow->fuel_type,
                $assetReportRow->make,
                $assetReportRow->model,
                $assetReportRow->location ?? '',
                isset($assetReportRow->last_service_date) ? $assetReportRow->last_service_date->format('Y-m-d') : '',
                isset($assetReportRow->service_level_start_date) ? $assetReportRow->service_level_start_date->format('Y-m-d') : '',
                $formattedJobDueDate,
                isset($assetReportRow->next_service_date) ? $assetReportRow->next_service_date->format('Y-m-d') : '',
                ($assetReportRow->job_stage === self::JOB_STAGE_ARCHIVED) ? '' : $assetReportRow->job_stage,
                $assetReportRow->service_level_name,
                $assetReportRow->last_mot_date,
                $formattedServiceDue,
                $nextScheduledAppointmentDate,
                $assetReportRow->no_access_visits,
                $cancellation,
            ]);
        }
    }

    protected function generateFileName(): string
    {
        $now = CarbonImmutable::now();

        return "CHL_InstalledEquipment_{$now->format('YmdHi')}.csv";
    }

    protected function generateStorageFileName(int $csvReportId): string
    {
        return "{$csvReportId}.csv";
    }
}
