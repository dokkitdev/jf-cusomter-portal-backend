<?php

namespace App\Support\CsvExport;

use Illuminate\Http\File;

class TmpCsvFile extends File
{
    protected $tmpFile;

    public function __construct()
    {
        $this->tmpFile = tmpfile();

        parent::__construct(stream_get_meta_data($this->tmpFile)['uri']);
    }

    public function put(array $data): void
    {
        fputcsv($this->tmpFile, $data);
    }
}
