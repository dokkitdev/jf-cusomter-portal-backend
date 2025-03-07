<?php

namespace App\Generators;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;

class PdfGenerator
{
    public function docxToPdf(string $filenameDocx): string
    {
        $path = Storage::disk('private_contracts_docs')->path($filenameDocx);

        $htmlContent = $this->convertDocxToHtml($path);

        $pdf = PDF::loadHTML($htmlContent);
        $pdf->setPaper('A4');

        $pdfFilename = Str::replace('.docx', '.pdf', $filenameDocx);

        $pdf->save(Storage::disk('private_contracts_docs')->path($pdfFilename));

        return $pdfFilename;
    }

    protected function convertDocxToHtml($docxPath): string
    {
        $phpWord = IOFactory::load($docxPath);

        $objWriter = IOFactory::createWriter($phpWord, 'HTML');

        ob_start();
        $objWriter->save('php://output');
        $html = ob_get_clean();

        return $html;
    }
}
