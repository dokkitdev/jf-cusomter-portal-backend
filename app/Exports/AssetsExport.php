<?php

namespace App\Exports;

use App\Services\AssetService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetsExport extends BaseExport implements FromCollection, WithHeadings, WithMapping
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
            'AssetID',
            'Customer',
            'Site UPRN',
            'Site',
            'Asset Name',
            'Location',
            'Make',
            'Model',
            'Last Test Result',
            'Last Test Date',
            'Service Level',
            'Next Service Date',
            'Expiry Date',
        ];
    }

    public function map($row): array
    {
        return [
            $row['simpro_asset_id'],
            Arr::get($row, 'site.customer.name'),
            (string) Arr::get($row, 'site.uprn'),
            Arr::get($row, 'site.name'),
            $row['name'],
            $row['location'],
            $row['make'],
            $row['model'],
            $row['last_test_result'],
            $row['last_test_date'] ? Carbon::parse($row['last_test_date'])->format('M d Y') : null,
            $row['service_level_name'],
            $row['next_service_date'] ? Carbon::parse($row['next_service_date'])->format('M d Y') : null,
            $row['expiry_date'] ? Carbon::parse($row['expiry_date'])->format('M d Y') : null,
        ];
    }
}
