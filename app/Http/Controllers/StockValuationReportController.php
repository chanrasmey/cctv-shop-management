<?php

namespace App\Http\Controllers;

use App\Exports\StockValuationReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\StockValuationReportService;
use Illuminate\Http\Request;

class StockValuationReportController extends Controller
{
    protected StockValuationReportService $service;

    protected PdfReportService $pdfService;

    public function __construct(
        StockValuationReportService $service,
        PdfReportService $pdfService
    ) {
        $this->service = $service;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $filters = $request->all();

        return view('reports.stock_valuation.index', [
            'items'      => $this->service->items($filters),
            'summary'    => $this->service->summary($filters),
            'categories' => $this->service->categories(),
            'brands'     => $this->service->brands(),
            'company'    => $this->service->company(),
            'filters'    => $filters,
        ]);
    }

    public function print(Request $request)
    {
        $filters = $request->all();

        return view('reports.stock_valuation.print', [
            'items'      => $this->service->itemsForPrint($filters),
            'summary'    => $this->service->summary($filters),
            'categories' => $this->service->categories(),
            'brands'     => $this->service->brands(),
            'company'    => $this->service->company(),
            'filters'    => $filters,
        ]);
    }

    public function pdf(Request $request)
    {
        $filters = $request->all();

        return $this->pdfService->stream('reports.stock_valuation.pdf', [
            'items'      => $this->service->itemsForPrint($filters),
            'summary'    => $this->service->summary($filters),
            'categories' => $this->service->categories(),
            'brands'     => $this->service->brands(),
            'company'    => $this->service->company(),
            'filters'    => $filters,
        ]);
    }

    public function excel(Request $request)
    {
        $writer = (new StockValuationReportExport())->export(
            $this->service->itemsForPrint($request->all())
        );

        return response()->streamDownload(
            function () use ($writer): void {
                $writer->save('php://output');
            },
            'stock-valuation-report.xlsx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}
