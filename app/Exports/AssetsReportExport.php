<?php

namespace App\Exports;

use App\Services\AssetService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsReportExport implements FromCollection, WithHeadings, WithMapping
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
            'Order#',
            'Customer',
            'Site Name',
            'Site Address',
            'Site Contact',
            'Completion Date',
            'Last CP12',
            'Next Scheduled Date',
            'Status',
            'Job Number',
            'No Access',
        ];
    }

    public function map($row): array
    {
        $completionDate = Arr::get($row, 'job.completion_date');
        $lastCP12Date = $row['last_test_date'] ?? $row['last_cp12_date'];
        $nextScheduledDate = Arr::get($row, 'next_schedule.date');

        $map = [
            Arr::get($row, 'site.uprn'),
            Arr::get($row, 'job.order_no'),
            Arr::get($row, 'job_customer.name'),
            Arr::get($row, 'site.name'),
            Arr::get($row, 'site.address'),
            Arr::get($row, 'site.primary_site_contact.name'),
            $completionDate ? Carbon::parse($completionDate)->format('M d Y') : null,
            $lastCP12Date ? Carbon::parse($lastCP12Date)->format('M d Y') : null,
            $nextScheduledDate ? Carbon::parse($nextScheduledDate)->format('M d Y') : null,
            Arr::get($row, 'job.job_status'),
            Arr::get($row, 'job.simpro_job_id'),
        ];

        $jobNoAccessDates = Arr::get($row, 'job.job_no_access_dates', []);

        foreach ($jobNoAccessDates as $jobNoAccessDate) {
            $map[] = $jobNoAccessDate['date'] ? Carbon::parse($jobNoAccessDate['date'])->format('M d Y') : null;
        }

        return $map;
    }
}
