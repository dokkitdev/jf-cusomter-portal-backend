<?php

namespace App\Exports;

use App\Services\JobService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JobsExport implements FromCollection, WithHeadings, WithMapping
{
    protected JobService $service;
    protected array $filters;

    public function __construct(JobService $service, array $filters)
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
            'Created Date',
            'Job#',
            'Order#',
            'Customer',
            'Site UPRN',
            'Site Name',
            'Postcode',
            'Cost Centre',
            'Priority',
            'Stage',
            'Status',
            'Made Safe',
            'Completion Date',
            'Due Date',
            'Scheduled Date',
            'Attachments'
        ];
    }

    public function map($row): array
    {
        $recentTime = null;

        if (Arr::get($row, 'recent_schedule.start_time') && Arr::get($row, 'recent_schedule.end_time')) {
            $startTime = Carbon::parse(Arr::get($row, 'recent_schedule.start_time'));
            $endTime = Carbon::parse(Arr::get($row, 'recent_schedule.end_time'));

            $recentTime = $startTime->format('M d Y H:i') . '-' . $endTime->format('H:i');
        }

        return [
            $row['date_created'] ? Carbon::parse($row['date_created'])->format('M d Y') : null,
            $row['simpro_job_id'],
            $row['order_no'],
            Arr::get($row, 'customer.name'),
            Arr::get($row, 'site.uprn'),
            Arr::get($row, 'site.name'),
            Arr::get($row, 'site.postal_code'),
            $row['cost_center_name'],
            $row['priority'],
            $row['stage'],
            $row['job_status'],
            $row['made_safe_date'] ? Carbon::parse($row['made_safe_date'])->format('M d Y H:i') : null,
            $row['completion_date'] ? Carbon::parse($row['completion_date'])->format('M d Y') : null,
            $row['due_date'] ? Carbon::parse($row['due_date'])->format('M d Y H:i') : null,
            $recentTime,
            Arr::get($row, 'job_attachments_count'),
        ];
    }
}
