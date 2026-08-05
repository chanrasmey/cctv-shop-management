<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProfitLossReportExport
{
    public function export(array $report): Xlsx
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Profit & Loss Report');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $row = 3;

        foreach ([
            'Sales Revenue' => 'sales_revenue',
            'Sales Returns' => 'sales_returns',
            'Net Sales' => 'net_sales',
            'Cost of Goods Sold' => 'cost_of_goods_sold',
            'Gross Profit' => 'gross_profit',
            'Purchase Amount' => 'purchase_amount',
            'Purchase Returns' => 'purchase_returns',
            'Net Purchases' => 'net_purchases',
            'Net Profit' => 'net_profit',
        ] as $label => $key) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $report[$key] ?? 0);
            $row++;
        }

        $sheet->getStyle("B3:B{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        foreach (range('A', 'B') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return new Xlsx($spreadsheet);
    }
}
