<?php

namespace App\Exports;

use App\Models\CompanySetting;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesReportExport
{
    public function export($sales, array $filters = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $company = CompanySetting::first();

        $row = 1;

        // Company Name
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue(
            'A1',
            $company->company_name ?? config('app.name')
        );

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Report Title
        $row = 3;

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Sales Report');

        $sheet->getStyle('A3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Filters
        $row = 5;

        $sheet->setCellValue(
            'A5',
            'Date : ' .
            ($filters['date_from'] ?? 'All') .
            '  -  ' .
            ($filters['date_to'] ?? 'All')
        );

        // Header
        $row = 7;

        $headers = [
            'Date',
            'Invoice',
            'Customer',
            'Items',
            'Grand Total',
            'Paid',
            'Balance',
        ];

        $column = 'A';

        foreach ($headers as $header) {

            $sheet->setCellValue($column . $row, $header);

            $column++;
        }

        $sheet->getStyle("A{$row}:G{$row}")
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'D9EAD3',
                    ],
                ],
            ]);

        $totalSales = 0;
        $totalPaid = 0;
        $totalBalance = 0;

        $row++;

        foreach ($sales as $sale) {

            $sheet->setCellValue('A' . $row, $sale->sale_date->format('Y-m-d'));
            $sheet->setCellValue('B' . $row, $sale->invoice_no);
            $sheet->setCellValue('C' . $row, optional($sale->customer)->name);
            $sheet->setCellValue('D' . $row, $sale->details->count());
            $sheet->setCellValue('E' . $row, $sale->grand_total);
            $sheet->setCellValue('F' . $row, $sale->paid_amount);
            $sheet->setCellValue('G' . $row, $sale->balance);

            $totalSales += $sale->grand_total;
            $totalPaid += $sale->paid_amount;
            $totalBalance += $sale->balance;

            $row++;
        }

        // Totals
        $sheet->setCellValue('D' . $row, 'TOTAL');
        $sheet->setCellValue('E' . $row, $totalSales);
        $sheet->setCellValue('F' . $row, $totalPaid);
        $sheet->setCellValue('G' . $row, $totalBalance);

        $sheet->getStyle("D{$row}:G{$row}")
            ->getFont()
            ->setBold(true);

        // Borders
        $sheet->getStyle("A7:G{$row}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Currency format
        $sheet->getStyle("E8:G{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Auto width
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        return new Xlsx($spreadsheet);
    }
}