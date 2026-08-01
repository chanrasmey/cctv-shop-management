<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportService
{
    /**
     * Download PDF.
     */
    public function download(
        string $view,
        array $data,
        string $filename,
        string $paper = 'a4',
        string $orientation = 'landscape'
    ) {
        return Pdf::loadView($view, $data)
            ->setPaper($paper, $orientation)
            ->download($filename);
    }

    /**
     * Stream PDF in browser.
     */
    public function stream(
        string $view,
        array $data,
        string $paper = 'a4',
        string $orientation = 'landscape'
    ) {
        return Pdf::loadView($view, $data)
            ->setPaper($paper, $orientation)
            ->stream();
    }
}