<?php

namespace App\Http\Controllers;

use App\Exports\SalesReturnReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\SalesReturnReportService;
use Illuminate\Http\Request;

class SalesReturnReportController extends Controller
{
    protected SalesReturnReportService $service;
    protected PdfReportService $pdfService;

    public function __construct(
        SalesReturnReportService $service,
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
            'customer_id',
            'status',
        ]);

        return view('reports.sales_returns.index', [
            'returns'   => $this->service->returns($filters),
            'summary'   => $this->service->summary($filters),
            'customers' => $this->service->customers(),
            'statuses'  => $this->service->statuses(),
            'filters'   => $filters,
        ]);
    }

    public function print(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'customer_id',
            'status',
        ]);

        return view('reports.sales_returns.print', [
            'returns'   => $this->service->returnsForPrint($filters),
            'summary'   => $this->service->summary($filters),
            'customers' => $this->service->customers(),
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
            'customer_id',
            'status',
        ]);

        $returns = $this->service->returnsForPrint($filters);

        $writer = (new SalesReturnReportExport())
            ->export($returns, $filters);

        $filename = 'Sales_Return_Report_' .
            now()->format('Ymd_His') .
            '.xlsx';

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
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
            'customer_id',
            'status',
        ]);

        return $this->pdfService->download(
            'reports.sales_returns.pdf',
            [
                'returns'   => $this->service->returnsForPrint($filters),
                'summary'   => $this->service->summary($filters),
                'customers' => $this->service->customers(),
                'statuses'  => $this->service->statuses(),
                'filters'   => $filters,
                'company'   => $this->service->company(),
                'generated' => now(),
            ],
            'Sales_Return_Report_' .
            now()->format('Ymd_His') .
            '.pdf'
        );
    }
}