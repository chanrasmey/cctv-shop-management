<?php

namespace App\Exports;

use App\Services\Reports\BalanceSheetReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BalanceSheetReportExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected array $filters;

    protected BalanceSheetReportService $service;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;

        $this->service = app(BalanceSheetReportService::class);
    }

    public function headings(): array
    {
        return [
            ['Balance Sheet Report'],
            [''],
            [
                'Description',
                'Amount',
            ],
        ];
    }

    public function array(): array
    {
        $report = $this->service->report($this->filters);

        return [

            [
                'ASSETS',
                '',
            ],

            [
                'Cash',
                $report['cash'],
            ],

            [
                'Inventory',
                $report['inventory'],
            ],

            [
                'Accounts Receivable',
                $report['accounts_receivable'],
            ],

            [
                'Other Assets',
                $report['other_assets'],
            ],

            [
                'Total Assets',
                $report['total_assets'],
            ],
                        [
                'LIABILITIES',
                '',
            ],

            [
                'Accounts Payable',
                $report['accounts_payable'],
            ],

            [
                'Other Liabilities',
                $report['other_liabilities'],
            ],

            [
                'Total Liabilities',
                $report['total_liabilities'],
            ],

            [
                '',
                '',
            ],

            [
                "OWNER'S EQUITY",
                '',
            ],

            [
                'Owner Capital',
                $report['owner_capital'],
            ],

            [
                'Retained Earnings',
                $report['retained_earnings'],
            ],

            [
                'Current Profit',
                $report['current_profit'],
            ],

            [
                'Total Equity',
                $report['total_equity'],
            ],

            [
                '',
                '',
            ],

            [
                'Liabilities + Equity',
                $report['liabilities_and_equity'],
            ],

            [
                'Balance Status',
                $report['balanced']
                    ? 'BALANCED'
                    : 'NOT BALANCED',
            ],
        ];
    }
    }