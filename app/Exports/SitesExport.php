<?php

namespace App\Exports;

use App\Services\SiteService;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SitesExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
{
    protected SiteService $service;
    protected array $filters;

    public function __construct(SiteService $service, array $filters)
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
            'Customer',
            'Site',
            'Site UPRN',
            'Site Address',
            'Postcode',
            'Primary Contact',
            'Open Jobs'
        ];
    }

    public function map($row): array
    {
        return [
            Arr::get($row, 'customer.name'),
            $row['name'],
            (string) $row['uprn'],
            $row['address'],
            $row['postal_code'],
            Arr::get($row, 'primary_site_contact.name'),
            $row['open_jobs_count'],
        ];
    }
}
