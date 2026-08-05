<?php

namespace App\Http\Controllers;

use App\Exports\BalanceSheetReportExport;
use App\Services\Reports\PdfReportService;
use App\Services\Reports\BalanceSheetReportService;
use Illuminate\Http\Request;

class BalanceSheetReportController extends Controller
{
    public function __construct(
        protected BalanceSheetReportService $service,
        protected PdfReportService $pdfService
    ) {
    }

    public function index(Request $request)
    {
        return view('reports.balance_sheet.index', [

            'report' => $this->service->report($request->all()),

            'company' => $this->service->company(),

            'filters' => $request->all(),

        ]);
    }
        public function print(Request $request)
    {
        return view('reports.balance_sheet.print', [

            'report' => $this->service->report($request->all()),

            'company' => $this->service->company(),

            'filters' => $request->all(),

        ]);
    }

    public function pdf(Request $request)
    {
        $data = [

            'report' => $this->service->report($request->all()),

            'company' => $this->service->company(),

            'filters' => $request->all(),

        ];

        return $this->pdfService->stream('reports.balance_sheet.pdf', $data);
    }

    public function excel(Request $request)
    {
        $writer = (new BalanceSheetReportExport())->export(
            $this->service->report($request->all())
        );

        return response()->streamDownload(
            function () use ($writer): void {
                $writer->save('php://output');
            },
            'Balance-Sheet-Report.xlsx',
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}
