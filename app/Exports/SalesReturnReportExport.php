<?php

namespace App\Exports;

use App\Models\CompanySetting;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesReturnReportExport
{
    public function export($returns, array $filters = [])
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $company = CompanySetting::first();

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
        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue(
            'A3',
            'Sales Return Report'
        );

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
        $sheet->setCellValue(
            'A5',
            'Date : '
            . ($filters['date_from'] ?? 'All')
            . ' - '
            . ($filters['date_to'] ?? 'All')
        );

        // Header
        $headers = [
            'Date',
            'Return No',
            'Invoice',
            'Customer',
            'Qty',
            'Amount',
            'Status',
        ];

        $row = 7;

        foreach (range('A', 'G') as $index => $column) {
            $sheet->setCellValue(
                $column . $row,
                $headers[$index]
            );
        }

        $sheet->getStyle("A{$row}:G{$row}")
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'F4CCCC',
                    ],
                ],
            ]);

        $row++;

        $totalQty = 0;
        $totalAmount = 0;

        foreach ($returns as $return) {

            $qty = $return->details->sum('qty');

            $sheet->setCellValue(
                'A'.$row,
                $return->return_date->format('Y-m-d')
            );

            $sheet->setCellValue(
                'B'.$row,
                $return->return_no
            );

            $sheet->setCellValue(
                'C'.$row,
                optional($return->sale)->invoice_no
            );

            $sheet->setCellValue(
                'D'.$row,
                optional($return->customer)->name
            );

            $sheet->setCellValue(
                'E'.$row,
                $qty
            );

            $sheet->setCellValue(
                'F'.$row,
                $return->grand_total
            );

            $sheet->setCellValue(
                'G'.$row,
                $return->status
            );

            $totalQty += $qty;
            $totalAmount += $return->grand_total;

            $row++;
        }

        // Totals
        $sheet->setCellValue('D'.$row, 'TOTAL');
        $sheet->setCellValue('E'.$row, $totalQty);
        $sheet->setCellValue('F'.$row, $totalAmount);

        $sheet->getStyle("D{$row}:F{$row}")
            ->getFont()
            ->setBold(true);

        // Borders
        $sheet->getStyle("A7:G{$row}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Currency
        $sheet->getStyle("F8:F{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        // Auto Width
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        return new Xlsx($spreadsheet);
    }
}