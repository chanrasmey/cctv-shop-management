<?php

namespace App\Http\Controllers;

use App\Exports\ProfitLossReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\ProfitLossReportService;
use Illuminate\Http\Request;

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

        return $this->pdfService->stream('reports.profit_loss.pdf', [
            'report' => $this->service->report($filters),
            'company' => $this->service->company(),
            'filters' => $filters,
        ]);
    }

    public function excel(Request $request)
    {
        $writer = (new ProfitLossReportExport())->export(
            $this->service->report($request->all())
        );

        return response()->streamDownload(
            function () use ($writer): void {
                $writer->save('php://output');
            },
            'profit-loss-report.xlsx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}
