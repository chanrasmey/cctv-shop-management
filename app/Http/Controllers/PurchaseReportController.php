<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\PurchaseReportService;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    protected PurchaseReportService $service;
    protected PdfReportService $pdfService;

    public function __construct(
        PurchaseReportService $service,
        PdfReportService $pdfService
    ) {
        $this->service = $service;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'supplier_id',
            'status',
        ]);

        return view('reports.purchases.index', [
            'purchases' => $this->service->purchases($filters),
            'summary'   => $this->service->summary($filters),
            'suppliers' => $this->service->suppliers(),
            'statuses'  => $this->service->statuses(),
            'filters'   => $filters,
        ]);
    }

    public function print(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'supplier_id',
            'status',
        ]);

        return view('reports.purchases.print', [
            'purchases' => $this->service->purchasesForPrint($filters),
            'summary'   => $this->service->summary($filters),
            'suppliers' => $this->service->suppliers(),
            'statuses'  => $this->service->statuses(),
            'filters'   => $filters,
            'company'   => $this->service->company(),
        ]);
    }

    public function excel(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'supplier_id',
            'status',
        ]);

        $writer = (new PurchaseReportExport())->export(
            $this->service->purchasesForPrint($filters),
            $filters
        );

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            'Purchase_Report_' . now()->format('Ymd_His') . '.xlsx',
            [
                'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    public function pdf(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'supplier_id',
            'status',
        ]);

        return $this->pdfService->download(
            'reports.purchases.pdf',
            [
                'purchases' => $this->service->purchasesForPrint($filters),
                'summary'   => $this->service->summary($filters),
                'suppliers' => $this->service->suppliers(),
                'statuses'  => $this->service->statuses(),
                'filters'   => $filters,
                'company'   => $this->service->company(),
                'generated' => now(),
            ],
            'Purchase_Report_' . now()->format('Ymd_His') . '.pdf'
        );
    }
}