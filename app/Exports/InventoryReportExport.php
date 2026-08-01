<?php

namespace App\Exports;

use App\Models\CompanySetting;
use App\Models\Product;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InventoryReportExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(string $filename = 'inventory-report.xlsx')
    {
        $spreadsheet = $this->buildSpreadsheet();

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function buildSpreadsheet(): Spreadsheet
    {
        $company = CompanySetting::first();

        $products = $this->query()->get();

        $sheet = (new Spreadsheet())->getActiveSheet();

        $row = 1;

        $sheet->setCellValue("A{$row}", $company->company_name ?? config('app.name'));
        $sheet->mergeCells("A{$row}:J{$row}");
        $sheet->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(16);

        $row++;

        $sheet->setCellValue("A{$row}", 'Inventory Report');
        $sheet->mergeCells("A{$row}:J{$row}");
        $sheet->getStyle("A{$row}")
            ->getFont()
            ->setBold(true)
            ->setSize(13);

        $row += 2;

        $sheet->setCellValue("A{$row}", 'Category');
        $sheet->setCellValue("B{$row}", $this->filters['category_name'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Brand');
        $sheet->setCellValue("B{$row}", $this->filters['brand_name'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Supplier');
        $sheet->setCellValue("B{$row}", $this->filters['supplier_name'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Stock Status');
        $sheet->setCellValue("B{$row}", $this->filters['stock_status'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Keyword');
        $sheet->setCellValue("B{$row}", $this->filters['keyword'] ?? '-');

        $row += 2;

        $headers = [
            'SKU',
            'Product',
            'Category',
            'Brand',
            'Unit',
            'Stock',
            'Avg Cost',
            'Sell Price',
            'Inventory Value',
            'Status',
        ];

        foreach ($headers as $index => $header) {

            $cell = chr(65 + $index) . $row;

            $sheet->setCellValue($cell, $header);

            $sheet->getStyle($cell)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'D9EAD3',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        $row++;
                $startDataRow = $row;

        foreach ($products as $product) {

            $inventoryValue = $product->stock * $product->average_cost;

            if ($product->stock <= 0) {
                $status = 'Out of Stock';
            } elseif ($product->stock <= ($product->minimum_stock ?? 0)) {
                $status = 'Low Stock';
            } else {
                $status = 'In Stock';
            }

            $sheet->setCellValue("A{$row}", $product->sku);
            $sheet->setCellValue("B{$row}", $product->product_name);
            $sheet->setCellValue("C{$row}", $product->category->category_name ?? '-');
            $sheet->setCellValue("D{$row}", $product->brand->brand_name ?? '-');
            $sheet->setCellValue("E{$row}", $product->unit->unit_name ?? '-');
            $sheet->setCellValue("F{$row}", (float) $product->stock);
            $sheet->setCellValue("G{$row}", (float) $product->average_cost);
            $sheet->setCellValue("H{$row}", (float) $product->sell_price);
            $sheet->setCellValue("I{$row}", (float) $inventoryValue);
            $sheet->setCellValue("J{$row}", $status);

            $sheet->getStyle("A{$row}:J{$row}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("G{$row}:I{$row}")
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $row++;
        }

        $endDataRow = $row - 1;

        $sheet->setCellValue("E{$row}", 'TOTAL');
        $sheet->getStyle("E{$row}")
            ->getFont()
            ->setBold(true);

        if ($endDataRow >= $startDataRow) {

            $sheet->setCellValue(
                "F{$row}",
                "=SUM(F{$startDataRow}:F{$endDataRow})"
            );

            $sheet->setCellValue(
                "I{$row}",
                "=SUM(I{$startDataRow}:I{$endDataRow})"
            );

        } else {

            $sheet->setCellValue("F{$row}", 0);
            $sheet->setCellValue("I{$row}", 0);

        }

        $sheet->getStyle("F{$row}:I{$row}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("F{$row}:I{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("E{$row}:J{$row}")
            ->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => 'FFF2CC',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $row += 2;

        $sheet->setCellValue(
            "A{$row}",
            'Generated : ' . now()->format('Y-m-d H:i:s')
        );
                return $spreadsheet;
    }

    protected function query()
    {
        return Product::query()
            ->with([
                'category',
                'brand',
                'unit',
                'supplier',
            ])

            ->when(
                !empty($this->filters['category_id']),
                fn ($query) => $query->where(
                    'category_id',
                    $this->filters['category_id']
                )
            )

            ->when(
                !empty($this->filters['brand_id']),
                fn ($query) => $query->where(
                    'brand_id',
                    $this->filters['brand_id']
                )
            )

            ->when(
                !empty($this->filters['supplier_id']),
                fn ($query) => $query->where(
                    'supplier_id',
                    $this->filters['supplier_id']
                )
            )

            ->when(
                !empty($this->filters['keyword']),
                function ($query) {

                    $keyword = trim($this->filters['keyword']);

                    $query->where(function ($q) use ($keyword) {

                        $q->where('product_name', 'like', "%{$keyword}%")
                          ->orWhere('sku', 'like', "%{$keyword}%")
                          ->orWhere('barcode', 'like', "%{$keyword}%");

                    });

                }
            )

            ->when(
                !empty($this->filters['stock_status']),
                function ($query) {

                    switch ($this->filters['stock_status']) {

                        case 'in_stock':
                            $query->where('stock', '>', 0);
                            break;

                        case 'low_stock':
                            $query->whereColumn('stock', '<=', 'minimum_stock')
                                  ->where('stock', '>', 0);
                            break;

                        case 'out_of_stock':
                            $query->where('stock', '<=', 0);
                            break;
                    }
                }
            )

            ->orderBy('product_name');
    }
}