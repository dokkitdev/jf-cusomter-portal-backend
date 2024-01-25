<?php

namespace App\Support\StreamedCsvExport;

class StreamedCsvExporter
{
    public function export(StreamedCsvExportInterface $export): void
    {
        $output = fopen('php://output', 'w');

        fputcsv($output, $export->headings());

        foreach ($export->generator() as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
    }
}
