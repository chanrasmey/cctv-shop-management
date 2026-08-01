<?php

namespace App\Http\Controllers;

use App\Exports\ProfitLossReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\ProfitLossReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProfitLossReportController extends Controller
{
    protected ProfitLossReportService $service;

    protected PdfReportService $pdfService;

    public function __construct(
        ProfitLossReportService $service,
        PdfReportService $pdfService
    ) {
        $this->service = $service;
        $this->pdfService = $pdfService;
    }

    public function index(Request $request)
    {
        $filters = $request->all();

        return view('reports.profit_loss.index', [
            'report' => $this->service->report($filters),
            'company' => $this->service->company(),
            'filters' => $filters,
        ]);
    }

    public function print(Request $request)
    {
        $filters = $request->all();

        return view('reports.profit_loss.print', [
            'report' => $this->service->report($filters),
            'company' => $this->service->company(),
            'filters' => $filters,
        ]);
    }

    public function pdf(Request $request)
    {
        $filters = $request->all();

        return $this->pdfService->stream(
            'reports.profit_loss.pdf',
            [
                'report' => $this->service->report($filters),
                'company' => $this->service->company(),
                'filters' => $filters,
            ],
            'profit-loss-report.pdf'
        );
    }

    public function excel(Request $request)
    {
        return Excel::download(
            new ProfitLossReportExport($request->all()),
            'profit-loss-report.xlsx'
        );
    }
}