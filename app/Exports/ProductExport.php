<?php

namespace App\Exports;

use App\Models\Product;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ProductExport
{
    public function download()
    {
        $products = Product::with([
            'category',
            'brand',
            'supplier',
            'unit',
        ])->orderBy('product_name')->get();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Products');

        /*
        |--------------------------------------------------------------------------
        | Report Title
        |--------------------------------------------------------------------------
        */

        $sheet->mergeCells('A1:L1');

        $sheet->setCellValue('A1', 'CCTV SHOP MANAGEMENT SYSTEM');

        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(18);

        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        $headers = [

            'SKU',
            'Barcode',
            'Product',
            'Category',
            'Brand',
            'Supplier',
            'Unit',
            'Buy Price',
            'Sell Price',
            'Stock',
            'Minimum',
            'Status',

        ];

        $column = 'A';

        foreach ($headers as $header) {

            $sheet->setCellValue($column . '3', $header);

            $column++;

        }

        $sheet->getStyle('A3:L3')->getFont()->setBold(true);

        $sheet->getStyle('A3:L3')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('1F4E78');

        $sheet->getStyle('A3:L3')->getFont()
            ->getColor()
            ->setARGB('FFFFFF');

        /*
        |--------------------------------------------------------------------------
        | Data
        |--------------------------------------------------------------------------
        */

        $row = 4;

        foreach ($products as $product) {

            $sheet->setCellValue('A' . $row, $product->sku);
            $sheet->setCellValue('B' . $row, $product->barcode);
            $sheet->setCellValue('C' . $row, $product->product_name);
            $sheet->setCellValue('D' . $row, $product->category->name);
            $sheet->setCellValue('E' . $row, $product->brand->name);
            $sheet->setCellValue('F' . $row, $product->supplier->company_name);
            $sheet->setCellValue('G' . $row, $product->unit->name);
            $sheet->setCellValue('H' . $row, $product->buy_price);
            $sheet->setCellValue('I' . $row, $product->sell_price);
            $sheet->setCellValue('J' . $row, $product->stock);
            $sheet->setCellValue('K' . $row, $product->minimum_stock);
            $sheet->setCellValue('L' . $row, $product->status ? 'Active' : 'Inactive');

            $row++;

        }

        /*
        |--------------------------------------------------------------------------
        | Currency Format
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle("H4:I{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        /*
        |--------------------------------------------------------------------------
        | Borders
        |--------------------------------------------------------------------------
        */

        $sheet->getStyle('A3:L' . ($row - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        /*
        |--------------------------------------------------------------------------
        | Auto Size
        |--------------------------------------------------------------------------
        */

        foreach (range('A', 'L') as $columnID) {

            $sheet->getColumnDimension($columnID)
                ->setAutoSize(true);

        }

        /*
        |--------------------------------------------------------------------------
        | Download
        |--------------------------------------------------------------------------
        */

        $writer = new Xlsx($spreadsheet);

        $filename = 'Products_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(

            function () use ($writer) {

                $writer->save('php://output');

            },

            $filename,

            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]

        );
    }
}