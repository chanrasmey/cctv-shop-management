<?php

namespace App\Exports;

use App\Services\Reports\ProfitLossReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProfitLossReportExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected array $filters;

    protected ProfitLossReportService $service;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;

        $this->service = app(ProfitLossReportService::class);
    }

    public function headings(): array
    {
        return [
            ['Profit & Loss Report'],
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
                'Sales Revenue',
                $report['sales_revenue'],
            ],

            [
                'Sales Returns',
                $report['sales_returns'],
            ],

            [
                'Net Sales',
                $report['net_sales'],
            ],
                        [
                'Cost of Goods Sold (COGS)',
                $report['cost_of_goods_sold'],
            ],

            [
                'Gross Profit',
                $report['gross_profit'],
            ],

            [
                'Purchases',
                $report['purchase_amount'],
            ],

            [
                'Purchase Returns',
                $report['purchase_returns'],
            ],

            [
                'Net Purchases',
                $report['net_purchases'],
            ],

            [
                'Net Profit / Loss',
                $report['net_profit'],
            ],
        ];
    }
    }