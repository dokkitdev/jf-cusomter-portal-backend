<?php

namespace App\Support\CsvExport;

use Generator;

interface CsvExportInterface
{
    public function headings(): array;
    public function generator(): Generator;
}
