<?php

namespace App\Http\Controllers;

use App\Exports\InventoryReportExport;
use App\Services\Reports\InventoryReportService;
use App\Services\Reports\PdfReportService;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function __construct(
        protected InventoryReportService $reportService,
        protected PdfReportService $pdfService
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'category_id',
            'brand_id',
            'supplier_id',
            'stock_status',
            'keyword',
        ]);

        return view('reports.inventory.index', [
            'products'   => $this->reportService->products($filters),
            'summary'    => $this->reportService->summary($filters),
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
            'category_id',
            'brand_id',
            'supplier_id',
            'stock_status',
            'keyword',
        ]);

        return view('reports.inventory.print', [
            'products'   => $this->reportService->productsForPrint($filters),
            'summary'    => $this->reportService->summary($filters),
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
            'category_id',
            'brand_id',
            'supplier_id',
            'stock_status',
            'keyword',
        ]);

        return $this->pdfService->download(
            'reports.inventory.pdf',
            [
                'products'   => $this->reportService->productsForPrint($filters),
                'summary'    => $this->reportService->summary($filters),
                'categories' => $this->reportService->categories(),
                'brands'     => $this->reportService->brands(),
                'suppliers'  => $this->reportService->suppliers(),
                'company'    => $this->reportService->company(),
                'filters'    => $filters,
                'generated'  => now(),
            ],
            'inventory-report.pdf'
        );
    }

    public function excel(Request $request)
    {
        return (new InventoryReportExport(
            $request->only([
                'category_id',
                'brand_id',
                'supplier_id',
                'stock_status',
                'keyword',
            ])
        ))->download();
    }
}