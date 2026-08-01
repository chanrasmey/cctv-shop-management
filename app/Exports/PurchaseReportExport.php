<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PurchaseReportExport
{
    public function export($purchases, array $filters = [])
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;

        $sheet->setCellValue("A{$row}", config('app.name'));
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(16);

        $row++;

        $sheet->setCellValue("A{$row}", 'Purchase Report');
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(14);

        $row += 2;

        $sheet->setCellValue("A{$row}", 'Date From');
        $sheet->setCellValue("B{$row}", $filters['date_from'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Date To');
        $sheet->setCellValue("B{$row}", $filters['date_to'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Supplier');
        $sheet->setCellValue("B{$row}", $filters['supplier_id'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Status');
        $sheet->setCellValue("B{$row}", $filters['status'] ?? 'All');

        $row += 2;

        $headers = [
            'Date',
            'Purchase No',
            'Supplier',
            'Items',
            'Grand Total',
            'Paid',
            'Balance',
            'Status',
        ];

        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);

            $sheet->setCellValue($column . $row, $header);
        }

        $sheet->getStyle("A{$row}:H{$row}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("A{$row}:H{$row}")
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFD9EAD3');

        $row++;

        foreach ($purchases as $purchase) {

            $sheet->setCellValue("A{$row}", optional($purchase->purchase_date)->format('Y-m-d'));
            $sheet->setCellValue("B{$row}", $purchase->purchase_no);
            $sheet->setCellValue("C{$row}", $purchase->supplier->company_name ?? '-');
            $sheet->setCellValue("D{$row}", $purchase->details->sum('qty'));
            $sheet->setCellValue("E{$row}", $purchase->grand_total);
            $sheet->setCellValue("F{$row}", $purchase->paid_amount);
            $sheet->setCellValue("G{$row}", $purchase->balance);
            $sheet->setCellValue("H{$row}", $purchase->status);

            $row++;
        }

        foreach (['E', 'F', 'G'] as $column) {
            $sheet->getStyle("{$column}9:{$column}{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle("A1:H{$row}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        $sheet->getStyle("D9:H{$row}")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return new Xlsx($spreadsheet);
    }
}