<?php

namespace App\Exports;

use App\Services\AssetService;
use App\Support\StreamedCsvExport\StreamedCsvExportInterface;
use Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class AssetsReportExport implements StreamedCsvExportInterface
{
    protected const CHUNK_SIZE = 20000;

    protected AssetService $service;
    protected array $filters;

    public function __construct(AssetService $service, array $filters)
    {
        $this->service = $service;
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'Site UPRN',
            'Asset ID',
            'Order#',
            'Customer',
            'Site Name',
            'Site Address',
            'Site Contact',
            'Completion Date',
            'Last Test Date',
            'Next Due (After Completion)',
            'Next Scheduled Date',
            'Status',
            'Job Number',
            'No Access 1',
            'No Access 2',
            'No Access 3',
            'No Access 4',
            'No Access 5',
        ];
    }

    public function generator(): Generator
    {
        $assets = $this->service->iterateByFilters($this->filters, self::CHUNK_SIZE);

        foreach ($assets as $asset) {
            $completionDate = Arr::get($asset, 'job.completion_date');
            $lastTestDate = $asset['sortable_date'];
            $nextDue = $asset['next_service_date'];
            $nextScheduledDate = Arr::get($asset, 'next_schedule.date');

            yield [
                (string)Arr::get($asset, 'site.uprn'),
                $asset['simpro_asset_id'],
                Arr::get($asset, 'job.order_no'),
                Arr::get($asset, 'job_customer.name'),
                Arr::get($asset, 'site.name'),
                Arr::get($asset, 'site.address'),
                Arr::get($asset, 'site.primary_site_contact.name'),
                $completionDate ? Carbon::parse($completionDate)->format('Y-m-d') : null,
                $lastTestDate ? Carbon::parse($lastTestDate)->format('Y-m-d') : null,
                $nextDue ? Carbon::parse($nextDue)->format('Y-m-d') : null,
                $nextScheduledDate ? Carbon::parse($nextScheduledDate)->format('Y-m-d') : null,
                Arr::get($asset, 'job.job_status'),
                Arr::get($asset, 'job.simpro_job_id'),
                $asset['no_access_date_1'] ? Carbon::parse($asset['no_access_date_1'])->format('Y-m-d') : null,
                $asset['no_access_date_2'] ? Carbon::parse($asset['no_access_date_2'])->format('Y-m-d') : null,
                $asset['no_access_date_3'] ? Carbon::parse($asset['no_access_date_3'])->format('Y-m-d') : null,
                $asset['no_access_date_4'] ? Carbon::parse($asset['no_access_date_4'])->format('Y-m-d') : null,
                $asset['no_access_date_5'] ? Carbon::parse($asset['no_access_date_5'])->format('Y-m-d') : null,
            ];
        }
    }
}
