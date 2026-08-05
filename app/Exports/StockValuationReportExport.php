<?php

namespace App\Exports;

use App\Services\Reports\StockValuationReportService;
use Illuminate\Support\Enumerable;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StockValuationReportExport
{
    public function export(Enumerable $items): Xlsx
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'SKU', 'Product', 'Category', 'Brand', 'Stock Qty', 'Average Cost',
            'Cost Value', 'Selling Price', 'Selling Value', 'Gross Profit', 'Stock Status',
        ];

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $row = 2;

        foreach ($items as $item) {
            $costValue = (float) $item->stock * (float) $item->average_cost;
            $sellingValue = (float) $item->stock * (float) $item->sell_price;

            $sheet->fromArray([
                $item->sku,
                $item->product_name,
                $item->category?->name ?? '',
                $item->brand?->name ?? '',
                $item->stock,
                $item->average_cost,
                $costValue,
                $item->sell_price,
                $sellingValue,
                $sellingValue - $costValue,
                app(StockValuationReportService::class)->stockStatus($item),
            ], null, "A{$row}");
            $row++;
        }

        $sheet->getStyle("E2:J{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        return new Xlsx($spreadsheet);
    }
}
