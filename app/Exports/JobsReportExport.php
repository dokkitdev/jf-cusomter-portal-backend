<?php

namespace App\Exports;

use App\Services\JobService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JobsReportExport implements FromCollection, WithHeadings, WithMapping
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
            'Status',
            'Site UPRN',
            'Job#',
            'Order#',
            'Description',
            'Response Type',
            'Site Address',
            'Call Date',
            'Target Date',
            'Made Safe',
            'Completed Date'
        ];
    }

    public function map($row): array
    {
        $status = null;

        if ($row['due_date'] && $row['made_safe_date']) {
            $dueDate = Carbon::parse($row['due_date']);
            $madeSafeDate = Carbon::parse($row['made_safe_date']);

            if ($madeSafeDate->lessThanOrEqualTo($dueDate)) {
                $status = 'On Time';
            } else {
                $status = 'Late';
            }
        }

        return [
            $status,
            Arr::get($row, 'site.uprn'),
            $row['simpro_job_id'],
            $row['order_no'],
            $row['description'] ? $this->getDescription($row['description']) : null,
            $row['priority'],
            Arr::get($row, 'site.address') . ' ' . Arr::get($row, 'site.postal_code'),
            $row['logged_create_date'] ? Carbon::parse($row['logged_create_date'])->format('M d Y H:i') : null,
            $row['due_date'] ? Carbon::parse($row['due_date'])->format('M d Y H:i') : null,
            $row['made_safe_date'] ? Carbon::parse($row['made_safe_date'])->format('M d Y H:i') : null,
            $row['logged_completion_date'] ? Carbon::parse($row['logged_completion_date'])->format('M d Y H:i') : null,
        ];
    }

    protected function getDescription(?string $string): ?string
    {
        $strippedString = str_replace("&nbsp;", ' ', strip_tags($string, 'null'));

        if ($strippedString) {
            $exploded = explode('.', $strippedString);
            $trimmed = array_map('trim', $exploded);
            $strippedString = implode('. ', $trimmed);
        }

        return $strippedString;
    }
}
