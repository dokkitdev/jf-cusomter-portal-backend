<?php

namespace App\Support\CsvExport;

class CsvExporter
{
    public function export(CsvExportInterface $export): TmpCsvFile
    {
        $file = new TmpCsvFile();

        $file->put($export->headings());

        foreach ($export->generator() as $row) {
            $file->put($row);
        }

        return $file;
    }
}
