<?php

namespace App\Support\StreamedCsvExport;

use Generator;

interface StreamedCsvExportInterface
{
    public function headings(): array;
    public function generator(): Generator;
}
