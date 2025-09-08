<?php

namespace App\Modules\Notify\Services;

use App\Modules\Notify\ApiClients\NotifySimproApiClient;
use App\Modules\Notify\DB\Models\NotifyAssetReport;
use App\Modules\Notify\DB\Services\NotifyAssetReportService;
use App\Modules\Notify\DB\Services\NotifyAssetReportValidationService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;

class AssetReportGenerator
{
    protected const CUSTOM_FIELD_ID_UPRN = 4;

    protected const CUSTOM_FIELD_NAME_REGEX_TYPE = '/^Type$/';
    protected const CUSTOM_FIELD_NAME_REGEX_MAKE = '/^Make$/';
    protected const CUSTOM_FIELD_NAME_REGEX_MODEL = '/^Model$/';
    protected const CUSTOM_FIELD_NAME_REGEX_LAST_MOT_DATE = '/^Last Years MOT Date$/';
    protected const CUSTOM_FIELD_NAME_REGEX_CANCELLATION = '/^Cancellation$/';
    protected const CUSTOM_FIELD_NAME_REGEX_LOCATION = '/^Location$/';
    protected const CUSTOM_FIELD_NAME_REGEX_FUEL_TYPE = '/Fuel Type/';

    protected const JOB_TAG_ID_NO_ACCESS_1 = 54;
    protected const JOB_TAG_ID_NO_ACCESS_2 = 55;
    protected const JOB_TAG_ID_NO_ACCESS_3 = 56;

    protected const NO_ACCESS_VISITS_TEXT_1 = 'No Access visits 1';
    protected const NO_ACCESS_VISITS_TEXT_2 = 'No Access visits 1, 2';
    protected const NO_ACCESS_VISITS_TEXT_3 = 'No Access visits 1, 2, 3';

    protected const KNOWN_ASSET_TEST_HISTORY_RESULTS = [
        'Pass',
        'Fail',
    ];

    protected const LAST_SERVICE_DATE_ERROR_DAYS = 425;
    protected const SERVICE_DUE_SOON_DAYS = 30;
    protected const LAST_SERVICE_DATE_AND_SERVICE_DUE_DIFF_ERROR_MONTHS_LESS = 12;
    protected const LAST_SERVICE_DATE_AND_SERVICE_DUE_DIFF_ERROR_MONTHS_MORE = 14;

    protected NotifyAssetReportService $notifyAssetReportService;
    protected NotifyAssetReportValidationService $notifyAssetReportValidationService;
    protected NotifySimproApiClient $notifySimproApiClient;

    protected int $customerId;

    public function __construct()
    {
        $this->notifyAssetReportService = app(NotifyAssetReportService::class);
        $this->notifyAssetReportValidationService = app(NotifyAssetReportValidationService::class);
        $this->notifySimproApiClient = app(NotifySimproApiClient::class);

        $this->customerId = config('notify.simpro.customer_id');
    }

    public function createOrUpdateAssetReport(int $siteId, int $assetId): void
    {
        $assetReportData = $this->prepareAssetReportData($siteId, $assetId);

        if (is_null($assetReportData)) {
            return;
        }

        $this->deleteAssetReport($siteId, $assetId);

        $assetReport = $this->notifyAssetReportService->create($assetReportData);

        foreach ($this->prepareAssetReportErrors($assetReport) as $error) {
            $this->notifyAssetReportValidationService->create($error);
        }
    }

    public function deleteAssetReport(int $siteId, int $assetId): void
    {
        $this->notifyAssetReportService->delete([
            'site_id' => $siteId,
            'asset_id' => $assetId,
        ]);
    }

    protected function prepareAssetReportErrors(NotifyAssetReport $assetReport): array
    {
        $errors = [];

        if (isset($assetReport->last_service_date)) {
            $diff = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($assetReport->last_service_date)->startOfDay());

            if ($diff > self::LAST_SERVICE_DATE_ERROR_DAYS) {
                $errors[] = __('notify::asset_reports.errors.last_service_date', ['diff' => $diff]);
            }
        }

        if (isset($assetReport->service_due)) {
            $diff = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($assetReport->service_due)->startOfDay(), false);

            if ($diff === 1) {
                $errors[] = __('notify::asset_reports.errors.service_due_tomorrow');
            } elseif ($diff <= self::SERVICE_DUE_SOON_DAYS) {
                $errors[] = __('notify::asset_reports.errors.service_due_soon', ['diff' => $diff]);
            }
        }

        if (isset($assetReport->last_service_date) && isset($assetReport->service_due)) {
            $lastServiceDate = Carbon::parse($assetReport->last_service_date)->startOfDay();
            $serviceDue = Carbon::parse($assetReport->service_due)->startOfDay();

            $diff = $lastServiceDate->diff($serviceDue);

            $months = abs($diff->m) + abs($diff->y) * CarbonInterface::MONTHS_PER_YEAR;
            $days = abs($diff->d);

            if (
                ($months < self::LAST_SERVICE_DATE_AND_SERVICE_DUE_DIFF_ERROR_MONTHS_LESS)
                || ($months > self::LAST_SERVICE_DATE_AND_SERVICE_DUE_DIFF_ERROR_MONTHS_MORE)
            ) {
                $errors[] = __('notify::asset_reports.errors.last_service_date_diff_service_due', ['months' => $months, 'days' => $days]);
            }
        }

        if (is_null($assetReport->uprn)) {
            $errors[] = __('notify::asset_reports.errors.no_uprn');
        }

        if (is_null($assetReport->fuel_type)) {
            $errors[] = __('notify::asset_reports.errors.no_fuel_type');
        }

        if (is_null($assetReport->make)) {
            $errors[] = __('notify::asset_reports.errors.no_make');
        }

        if (is_null($assetReport->model)) {
            $errors[] = __('notify::asset_reports.errors.no_model');
        }

        return array_map(function (string $error) use ($assetReport) {
            return [
                'site_id' => $assetReport->site_id,
                'uprn' => $assetReport->uprn,
                'fuel_type' => $assetReport->fuel_type,
                'asset_type' => $assetReport->asset_type,
                'asset_id' => $assetReport->asset_id,
                'asset_report_id' => $assetReport->id,
                'error' => $error,
            ];
        }, $errors);
    }

    protected function prepareAssetReportData(int $siteId, int $assetId): ?array
    {
        $siteData = $this->notifySimproApiClient->getSite($siteId);

        if (!$this->checkSiteCustomer($siteData['Customers'])) {
            return null;
        }

        $assetData = $this->notifySimproApiClient->getSiteAsset($siteId, $assetId);

        if ($assetData['Archived']) {
            $this->deleteAssetReport($siteId, $assetId);
            return null;
        }

        $todayDate = $this->getTodayDate();

        $latestServiceLevel = $this->notifySimproApiClient->getSiteAssetLatestServiceLevel($siteId, $assetId);
        $testHistory = $this->notifySimproApiClient->getSiteAssetTestHistory($siteId, $assetId);
        $latestTestHistoryData = Arr::first($testHistory);
        $jobData = isset($latestTestHistoryData) ? $this->notifySimproApiClient->getJob($latestTestHistoryData['Job']['ID']) : null;
        $latestScheduleData = isset($latestTestHistoryData) ? $this->notifySimproApiClient->getJobLatestSchedule($latestTestHistoryData['Job']['ID']) : null;

        $siteCustomFields = $siteData['CustomFields'];
        $assetCustomFields = $assetData['CustomFields'];
        $jobCustomFields = Arr::get($jobData, 'CustomFields', []);

        if ($this->existsTagById(Arr::get($jobData, 'Tags', []), self::JOB_TAG_ID_NO_ACCESS_3)) {
            $noAccessVisits = self::NO_ACCESS_VISITS_TEXT_3;
        } elseif ($this->existsTagById(Arr::get($jobData, 'Tags', []), self::JOB_TAG_ID_NO_ACCESS_2)) {
            $noAccessVisits = self::NO_ACCESS_VISITS_TEXT_2;
        } elseif ($this->existsTagById(Arr::get($jobData, 'Tags', []), self::JOB_TAG_ID_NO_ACCESS_1)) {
            $noAccessVisits = self::NO_ACCESS_VISITS_TEXT_1;
        } else {
            $noAccessVisits = null;
        }

        if (isset($latestScheduleData) && !empty($latestScheduleData['Blocks'])) {
            $block = $latestScheduleData['Blocks'][0];
            $nextScheduledAppointmentDate = "{$latestScheduleData['Date']} {$block['StartTime']} - {$block['EndTime']}";
        } else {
            $nextScheduledAppointmentDate = null;
        }

        $nextServiceDate = Arr::get($latestServiceLevel, 'ServiceDate');

        $jobDueDate = Arr::get($latestTestHistoryData, 'Job.DueDate');
        if (isset($latestServiceLevel) && ($jobDueDate < Carbon::now()->format('Y-m-d'))) {
            $jobDueDate = $latestServiceLevel['ServiceDate'];
        }

        return [
            'site_id' => $siteData['ID'],
            'asset_id' => $assetData['ID'],
            'uprn' => $this->findCustomFieldById($siteCustomFields, self::CUSTOM_FIELD_ID_UPRN),
            'asset_type' => $assetData['AssetType']['Name'],
            'type' => $this->findCustomFieldByNameRegex($assetCustomFields, self::CUSTOM_FIELD_NAME_REGEX_TYPE),
            'fuel_type' => $this->findCustomFieldByNameRegex($assetCustomFields, self::CUSTOM_FIELD_NAME_REGEX_FUEL_TYPE),
            'make' => $this->findCustomFieldByNameRegex($assetCustomFields, self::CUSTOM_FIELD_NAME_REGEX_MAKE),
            'model' => $this->findCustomFieldByNameRegex($assetCustomFields, self::CUSTOM_FIELD_NAME_REGEX_MODEL),
            'last_service_date' => $this->findLastServiceDateInTestHistory($testHistory),
            'service_level_start_date' => $assetData['StartDate'],
            'job_due_date' => $jobDueDate,
            'next_service_date' => $nextServiceDate,
            'job_stage' => Arr::get($jobData, 'Stage'),
            'service_level_name' => Arr::get($latestServiceLevel, 'ServiceLevel.Name'),
            'last_mot_date' => $this->findCustomFieldByNameRegex($assetCustomFields, self::CUSTOM_FIELD_NAME_REGEX_LAST_MOT_DATE),
            'service_due' => (empty($jobDueDate) || ($jobDueDate < $todayDate))
                ? $nextServiceDate
                : $jobDueDate,
            'next_scheduled_appointment_date' => $nextScheduledAppointmentDate,
            'no_access_visits' => $noAccessVisits,
            'location' => $this->findCustomFieldByNameRegex($assetCustomFields, self::CUSTOM_FIELD_NAME_REGEX_LOCATION),
            'cancellation' => $this->findCustomFieldByNameRegex($jobCustomFields, self::CUSTOM_FIELD_NAME_REGEX_CANCELLATION),
        ];
    }

    protected function findLastServiceDateInTestHistory(array $testHistory): ?string
    {
        foreach ($testHistory as $testHistoryData) {
            if (in_array($testHistoryData['TestRecord']['Result'], self::KNOWN_ASSET_TEST_HISTORY_RESULTS)) {
                return $testHistoryData['TestRecord']['Date'];
            }
        }

        return null;
    }

    protected function existsTagById(array $tags, int $tagId): bool
    {
        foreach ($tags as $tag) {
            if ($tag['ID'] === $tagId) {
                return true;
            }
        }

        return false;
    }

    protected function findCustomFieldByNameRegex(array $customFields, string $customFieldNamePattern): ?string
    {
        foreach ($customFields as $customField) {
            if (preg_match($customFieldNamePattern, $customField['CustomField']['Name'])) {
                return $customField['Value'];
            }
        }

        return null;
    }

    protected function findCustomFieldById(array $customFields, int $customFieldId): ?string
    {
        foreach ($customFields as $customField) {
            if ($customField['CustomField']['ID'] === $customFieldId) {
                return $customField['Value'];
            }
        }

        return null;
    }

    protected function checkSiteCustomer(array $siteCustomers): bool
    {
        foreach ($siteCustomers as $siteCustomer) {
            if ($siteCustomer['ID'] === $this->customerId) {
                return true;
            }
        }

        return false;
    }

    protected function getTodayDate(): string
    {
        $today = Carbon::now();

        if ($today->hour > 16) {
            $today->addDay();
        }

        return $today->format('Y-m-d');
    }
}
