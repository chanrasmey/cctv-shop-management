<?php

namespace App\Http\Controllers;

use App\Exports\PurchaseReturnReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\PurchaseReturnReportService;
use Illuminate\Http\Request;

class PurchaseReturnReportController extends Controller
{
    protected PurchaseReturnReportService $service;
    protected PdfReportService $pdfService;

    public function __construct(
        PurchaseReturnReportService $service,
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

        return view('reports.purchase_returns.index', [
            'returns'   => $this->service->returns($filters),
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

        return view('reports.purchase_returns.print', [
            'returns'   => $this->service->returnsForPrint($filters),
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

        $writer = (new PurchaseReturnReportExport())->export(
            $this->service->returnsForPrint($filters),
            $filters
        );

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            'Purchase_Return_Report_' . now()->format('Ymd_His') . '.xlsx',
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
            'reports.purchase_returns.pdf',
            [
                'returns'   => $this->service->returnsForPrint($filters),
                'summary'   => $this->service->summary($filters),
                'suppliers' => $this->service->suppliers(),
                'statuses'  => $this->service->statuses(),
                'filters'   => $filters,
                'company'   => $this->service->company(),
                'generated' => now(),
            ],
            'Purchase_Return_Report_' . now()->format('Ymd_His') . '.pdf'
        );
    }
}