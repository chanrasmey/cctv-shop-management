<?php

namespace App\Exports;

use Illuminate\Support\Enumerable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockCardReportExport
{
    public function export(Enumerable $movements): Xlsx
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'Date', 'Reference', 'Product', 'Movement Type',
            'Stock In', 'Stock Out', 'Balance', 'Unit Cost', 'Remark',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $row = 2;

        foreach ($movements as $movement) {
            $sheet->fromArray([
                $movement->created_at?->format('Y-m-d'),
                $movement->reference_no,
                $movement->product?->product_name,
                $movement->movement_type,
                $movement->qty_in,
                $movement->qty_out,
                $movement->balance,
                $movement->unit_cost,
                $movement->remark,
            ], null, "A{$row}");
            $row++;
        }

        $sheet->getStyle("E2:H{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return new Xlsx($spreadsheet);
    }
}
