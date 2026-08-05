<?php

namespace App\Http\Controllers;

use App\Exports\SalesReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function __construct(
        protected SalesReportService $service,
        protected PdfReportService $pdfService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'date_from',
            'date_to',
            'customer_id',
            'status',
        ]);

        return view('reports.sales.index', [
            'sales' => $this->service->sales($filters),
            'summary' => $this->service->summary($filters),
            'customers' => $this->service->customers(),
            'statuses' => $this->service->statuses(),
            'filters' => $filters,
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

        return view('reports.sales.print', [
            'sales' => $this->service->salesForPrint($filters),
            'summary' => $this->service->summary($filters),
            'customers' => $this->service->customers(),
            'statuses' => $this->service->statuses(),
            'filters' => $filters,
            'company' => $this->service->company(),
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

        $writer = (new SalesReportExport())->export(
            $this->service->salesForPrint($filters),
            $filters
        );

        return response()->streamDownload(
            function () use ($writer): void {
                $writer->save('php://output');
            },
            'Sales_Report_'.now()->format('Ymd_His').'.xlsx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
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
            'reports.sales.pdf',
            [
                'sales' => $this->service->salesForPrint($filters),
                'summary' => $this->service->summary($filters),
                'customers' => $this->service->customers(),
                'statuses' => $this->service->statuses(),
                'filters' => $filters,
                'company' => $this->service->company(),
                'generated' => now(),
            ],
            'Sales_Report_'.now()->format('Ymd_His').'.pdf'
        );
    }
}
