<?php

namespace App\Exports;

use App\Services\AssetService;
use App\Support\StreamedCsvExport\StreamedCsvExportInterface;
use Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class AssetsExport implements StreamedCsvExportInterface
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

    public function generator(): Generator
    {
        $assets = $this->service->iterateByFilters($this->filters, self::CHUNK_SIZE);

        foreach ($assets as $asset) {
            yield [
                $asset['simpro_asset_id'],
                Arr::get($asset, 'site.customer.name'),
                (string) Arr::get($asset, 'site.uprn'),
                Arr::get($asset, 'site.name'),
                $asset['name'],
                $asset['location'],
                $asset['make'],
                $asset['model'],
                $asset['last_test_result'],
                $asset['last_test_date'] ? Carbon::parse($asset['last_test_date'])->format('M d Y') : null,
                $asset['service_level_name'],
                $asset['next_service_date'] ? Carbon::parse($asset['next_service_date'])->format('M d Y') : null,
                $asset['expiry_date'] ? Carbon::parse($asset['expiry_date'])->format('M d Y') : null,
            ];
        }
    }
}
