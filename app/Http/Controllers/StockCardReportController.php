<?php

namespace App\Http\Controllers;

use App\Exports\StockCardReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\StockCardReportService;
use Illuminate\Http\Request;

class StockCardReportController extends Controller
{
    public function __construct(
        protected StockCardReportService $reportService,
        protected PdfReportService $pdfService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'product_id',
            'category_id',
            'brand_id',
            'supplier_id',
            'date_from',
            'date_to',
        ]);

        return view('reports.stock_card.index', [
            'movements'  => $this->reportService->movements($filters),
            'summary'    => $this->reportService->summary($filters),
            'products'   => $this->reportService->products(),
            'categories' => $this->reportService->categories(),
            'brands'     => $this->reportService->brands(),
            'suppliers'  => $this->reportService->suppliers(),
            'company'    => $this->reportService->company(),
            'filters'    => $filters,
        ]);
    }

    public function print(Request $request)
    {
        $filters = $request->only([
            'product_id',
            'category_id',
            'brand_id',
            'supplier_id',
            'date_from',
            'date_to',
        ]);

        return view('reports.stock_card.print', [
            'movements'  => $this->reportService->movementsForPrint($filters),
            'summary'    => $this->reportService->summary($filters),
            'products'   => $this->reportService->products(),
            'categories' => $this->reportService->categories(),
            'brands'     => $this->reportService->brands(),
            'suppliers'  => $this->reportService->suppliers(),
            'company'    => $this->reportService->company(),
            'filters'    => $filters,
            'generated'  => now(),
        ]);
    }

    public function pdf(Request $request)
    {
        $filters = $request->only([
            'product_id',
            'category_id',
            'brand_id',
            'supplier_id',
            'date_from',
            'date_to',
        ]);

        return $this->pdfService->download(
            'reports.stock_card.pdf',
            [
                'movements'  => $this->reportService->movementsForPrint($filters),
                'summary'    => $this->reportService->summary($filters),
                'products'   => $this->reportService->products(),
                'categories' => $this->reportService->categories(),
                'brands'     => $this->reportService->brands(),
                'suppliers'  => $this->reportService->suppliers(),
                'company'    => $this->reportService->company(),
                'filters'    => $filters,
                'generated'  => now(),
            ],
            'stock-card-report.pdf'
        );
    }

    public function excel(Request $request)
    {
        return (new StockCardReportExport(
            $request->only([
                'product_id',
                'category_id',
                'brand_id',
                'supplier_id',
                'date_from',
                'date_to',
            ])
        ))->download();
    }
}
