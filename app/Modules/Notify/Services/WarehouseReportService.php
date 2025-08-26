<?php

namespace App\Modules\Notify\Services;

use App\Modules\Notify\ApiClients\NotifySimproApiClient;
use App\Modules\Notify\DB\Models\NotifyReport;
use App\Modules\Notify\DB\Models\ParsingLog;
use App\Modules\Notify\DB\Services\NotifyReportService;
use App\Modules\Notify\DB\Services\ParsingLogService;
use App\Modules\Notify\Jobs\WarehouseReportJob;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTimeImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class WarehouseReportService
{
    protected NotifyReportService $notifyReportService;
    protected NotifySimproApiClient $notifySimproApiClient;
    protected ParsingLogService $parsingLogService;

    public function __construct()
    {
        $this->notifyReportService = app(NotifyReportService::class);
        $this->notifySimproApiClient = app(NotifySimproApiClient::class);
        $this->parsingLogService = app(ParsingLogService::class);
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

        dispatch(new WarehouseReportJob($daysCount, $warehouseProjectReport->id, $warehouseServiceReport->id));;
    }

    public function generateReport(int $daysCount, int $warehouseProjectReportId, int $warehouseServiceReportId): void
    {
        $start = Carbon::now()->addDay()->startOfDay();
        $end = Carbon::now()->addDays($daysCount + 1)->startOfDay();

        $period = CarbonPeriod::create($start, $end)->excludeEndDate();

        $reportData = [];

        foreach ($period as $date) {
            $reportJobs = $this->parseSchedulesByDate(DateTimeImmutable::createFromMutable($date->toDateTime()));

            $reportData[] = [
                'date' => $date,
                'report_jobs' => $reportJobs,
            ];
        }

        $this->saveReport($reportData, NotifySimproApiClient::JOB_TYPE_PROJECT, $warehouseProjectReportId);
        $this->notifyReportService->update($warehouseProjectReportId, [
            'is_finished' => true,
        ]);

        $this->saveReport($reportData, NotifySimproApiClient::JOB_TYPE_SERVICE, $warehouseServiceReportId);
        $this->notifyReportService->update($warehouseServiceReportId, [
            'is_finished' => true,
        ]);
    }

    public function failedReportGeneration(int $warehouseProjectReportId, int $warehouseServiceReportId): void
    {
        $this->notifyReportService->delete($warehouseProjectReportId);
        $this->notifyReportService->delete($warehouseServiceReportId);
    }

    protected function saveReport(array $reportData, string $jobType, int $reportId): void
    {
        $reportData = $this->filterReportData($reportData, $jobType);

        $htmlReportContent = View::make('notify::warehouse_report', [
            'type' => $jobType,
            'reportData' => $reportData,
        ])->render();

        $tempFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tempFile)['uri'];
        $tempFileName = basename($tempFilePath);
        fwrite($tempFile, $htmlReportContent);

        $storage = Storage::disk('pdf_reports');

        exec("libreoffice --headless --writer --convert-to pdf:writer_pdf_Export {$tempFilePath} --outdir {$storage->path('')}");

        $storage->move("{$tempFileName}.pdf", "{$reportId}.pdf");
    }

    protected function filterReportData(array $reportData, string $jobType): array
    {
        foreach ($reportData as &$reportOneDate) {
            foreach ($reportOneDate['report_jobs'] as &$reportJob) {
                $reportJob['parts'] = array_filter($reportJob['parts'], function (array $part) {
                    return $part['assigned'] === 0;
                });
            }

            $reportOneDate['report_jobs'] = array_filter($reportOneDate['report_jobs'], function (array $reportJob) use ($jobType) {
                return !empty($reportJob['parts']) && ($reportJob['type'] === $jobType);
            });
        }

        return $reportData;
    }

    /**
     * @return array
     * [
     *      <job_id> => [
     *          'id' => <job_id>,
     *          'type' => 'Project' | 'Service',
     *          'site_name' => string,
     *          'engineer_name' => string,
     *          'parts' => [
     *              [
     *                  'stock_name' => string,
     *                  'part_no' => string,
     *                  'required' => int,
     *                  'assigned' => int,
     *                  'needed' => int,
     *                  'storage_location' => string,
     *              ],
     *              ...
     *          ],
     *      ],
     *      ...
     * ]
     */
    protected function parseSchedulesByDate(DateTimeImmutable $date): array
    {
        $totalJobsCount = 0;
        $successJobsCount = 0;
        $jobIds = [];
        $errorReasons = [];

        $reportJobs = [];

        $schedules = $this->notifySimproApiClient->getSchedulesByDate($date);

        foreach ($schedules as $schedule) {
            $jobId = (int) Arr::first(explode('-', $schedule['Reference']));

            $totalJobsCount++;
            $jobIds[] = $jobId;

            $job = $this->notifySimproApiClient->getJob($jobId);

            $jobErrors = $this->parseJobErrors($job);

            if (!empty($jobErrors)) {
                $errorReasons = array_merge($errorReasons, $jobErrors);

                continue;
            }

            $successJobsCount++;

            $reportJobs[$jobId] = [
                'id' => $jobId,
                'type' => $job['Type'],
                'site_name' => $job['Site']['Name'],
                'engineer_name' => $schedule['Staff']['Name'],
                'parts' => [],
            ];

            foreach ($job['Sections'] as $section) {
                foreach ($section['CostCenters'] as $costCenter) {
                    if ($costCenter['Total']['ExTax'] === 0) {
                        continue;
                    }

                    $stock = $this->notifySimproApiClient->getCostCenterStock($job['ID'], $section['ID'], $costCenter['ID']);

                    foreach ($stock as $stockItem) {
                        if ($stockItem['Quantity']['Required'] <= $stockItem['Quantity']['Assigned']) {
                            continue;
                        }

                        $catalog = $this->notifySimproApiClient->getCatalog($stockItem['Catalog']['ID']);

                        $reportJobs[$jobId]['parts'][] = [
                            'stock_name' => $stockItem['Catalog']['Name'],
                            'part_no' => $stockItem['Catalog']['PartNo'],
                            'required' => $stockItem['Quantity']['Required'],
                            'assigned' => $stockItem['Quantity']['Assigned'],
                            'needed' => $stockItem['Quantity']['Required'] - $stockItem['Quantity']['Assigned'],
                            'storage_location' => $catalog['StorageLocation'],
                        ];
                    }
                }
            }
        }

        $this->parsingLogService->create([
            'parsing_type' => ParsingLog::PARSING_TYPE_WAREHOUSE_REPORT,
            'parsing_date' => $date,
            'total_count' => $totalJobsCount,
            'success_count' => $successJobsCount,
            'ids' => $jobIds,
            'error_reasons' => $errorReasons,
        ]);

        return $reportJobs;
    }

    protected function parseJobErrors(array $simproJob): array
    {
        $errorReasons = [];

        if (!in_array($simproJob['Stage'], [NotifySimproApiClient::JOB_STAGE_PENDING, NotifySimproApiClient::JOB_STAGE_PROGRESS])) {
            $errorReasons[] = [
                'job_id' => $simproJob['ID'],
                'reason' => ParsingLog::ERROR_REASON_WAREHOUSE_REPORT_UNKNOWN_JOB_STAGE,
            ];
        }

        if (!in_array($simproJob['Type'], [NotifySimproApiClient::JOB_TYPE_PROJECT, NotifySimproApiClient::JOB_TYPE_SERVICE])) {
            $errorReasons[] = [
                'job_id' => $simproJob['ID'],
                'reason' => ParsingLog::ERROR_REASON_WAREHOUSE_REPORT_UNKNOWN_JOB_TYPE,
            ];
        }

        return $errorReasons;
    }
}
