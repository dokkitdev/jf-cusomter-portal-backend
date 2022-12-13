<?php

namespace App\Exports;

use App\Services\AssetService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsReportExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected AssetService $service;
    protected array $filters;

    public function __construct(AssetService $service, array $filters)
    {
        $this->service = $service;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->service->search($this->filters);
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

    public function map($row): array
    {
        $completionDate = Arr::get($row, 'job.completion_date');
        $lastTestDate = $row['sortable_date'];
        $nextDue = $row['next_service_date'];
        $nextScheduledDate = Arr::get($row, 'next_schedule.date');

        return [
            (string) Arr::get($row, 'site.uprn'),
            $row['simpro_asset_id'],
            Arr::get($row, 'job.order_no'),
            Arr::get($row, 'job_customer.name'),
            Arr::get($row, 'site.name'),
            Arr::get($row, 'site.address'),
            Arr::get($row, 'site.primary_site_contact.name'),
            $completionDate ? Carbon::parse($completionDate)->format('Y-m-d') : null,
            $lastTestDate ? Carbon::parse($lastTestDate)->format('Y-m-d') : null,
            $nextDue ? Carbon::parse($nextDue)->format('Y-m-d') : null,
            $nextScheduledDate ? Carbon::parse($nextScheduledDate)->format('Y-m-d') : null,
            Arr::get($row, 'job.job_status'),
            Arr::get($row, 'job.simpro_job_id'),
            $row['no_access_date_1'] ? Carbon::parse($row['no_access_date_1'])->format('Y-m-d') : null,
            $row['no_access_date_2'] ? Carbon::parse($row['no_access_date_2'])->format('Y-m-d') : null,
            $row['no_access_date_3'] ? Carbon::parse($row['no_access_date_3'])->format('Y-m-d') : null,
            $row['no_access_date_4'] ? Carbon::parse($row['no_access_date_4'])->format('Y-m-d') : null,
            $row['no_access_date_5'] ? Carbon::parse($row['no_access_date_5'])->format('Y-m-d') : null,
        ];
    }
}
