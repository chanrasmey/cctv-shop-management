<?php

namespace App\Http\Controllers;

use App\Services\Reports\PdfReportService;
use App\Services\Reports\StockReportService;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    protected StockReportService $service;
    protected PdfReportService $pdfService;

    public function __construct(
        StockReportService $service,
        PdfReportService $pdfService
    ) {
        $this->service = $service;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'category_id',
            'brand_id',
            'search',
        ]);

        return view('reports.stock.index', [
            'products'   => $this->service->products($filters),
            'summary'    => $this->service->summary($filters),
            'categories' => $this->service->categories(),
            'brands'     => $this->service->brands(),
            'filters'    => $filters,
        ]);
    }

    public function print(Request $request)
    {
        $filters = $request->only([
            'category_id',
            'brand_id',
            'search',
        ]);

        return view('reports.stock.print', [
            'products'   => $this->service->products($filters),
            'summary'    => $this->service->summary($filters),
            'company'    => $this->service->company(),
            'filters'    => $filters,
        ]);
    }

    public function excel(Request $request)
    {
        return redirect()
            ->route('reports.stock')
            ->with('info', 'Stock Excel export will be implemented next.');
    }

    public function pdf(Request $request)
    {
        $filters = $request->only([
            'category_id',
            'brand_id',
            'search',
        ]);

        return $this->pdfService->download(
            'reports.stock.pdf',
            [
                'products'  => $this->service->products($filters),
                'summary'   => $this->service->summary($filters),
                'company'   => $this->service->company(),
                'filters'   => $filters,
                'generated' => now(),
            ],
            'Stock_Report_' . now()->format('Ymd_His') . '.pdf'
        );
    }
}