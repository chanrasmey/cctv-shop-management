<?php

namespace App\Http\Controllers;

use App\Exports\BalanceSheetReportExport;
use App\Services\PdfReportService;
use App\Services\Reports\BalanceSheetReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        return $this->pdfService->stream(

            'reports.balance_sheet.pdf',

            $data,

            'Balance-Sheet-Report.pdf'

        );
    }

    public function excel(Request $request)
    {
        return Excel::download(

            new BalanceSheetReportExport($request->all()),

            'Balance-Sheet-Report.xlsx'

        );
    }
    }