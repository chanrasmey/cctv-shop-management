<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BalanceSheetReportExport
{
    public function export(array $report): Xlsx
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Balance Sheet Report');
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $row = 3;

        foreach ([
            'Assets' => $report['assets'] ?? [],
            'Liabilities' => $report['liabilities'] ?? [],
            'Equity' => $report['equity'] ?? [],
        ] as $section => $items) {
            $sheet->setCellValue("A{$row}", $section);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $row++;

            foreach ($items as $item) {
                $sheet->setCellValue("B{$row}", $item['name'] ?? '');
                $sheet->setCellValue("C{$row}", $item['amount'] ?? 0);
                $row++;
            }
        }

        $sheet->setCellValue("B{$row}", 'Total Assets');
        $sheet->setCellValue("C{$row}", $report['total_assets'] ?? 0);
        $row++;
        $sheet->setCellValue("B{$row}", 'Total Liabilities');
        $sheet->setCellValue("C{$row}", $report['total_liabilities'] ?? 0);
        $row++;
        $sheet->setCellValue("B{$row}", 'Total Equity');
        $sheet->setCellValue("C{$row}", $report['total_equity'] ?? 0);

        $sheet->getStyle("C1:C{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        foreach (range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return new Xlsx($spreadsheet);
    }
}
