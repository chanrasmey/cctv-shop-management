<?php

namespace App\Exports;

use App\Services\Reports\StockValuationReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockValuationReportExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    protected array $filters;

    protected StockValuationReportService $service;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->service = app(StockValuationReportService::class);
    }

    public function collection()
    {
        return $this->service
            ->itemsForPrint($this->filters)
            ->map(function ($item) {

                $costValue = $item->stock * $item->average_cost;

                $sellingValue = $item->stock * $item->sell_price;

                $grossProfit = $sellingValue - $costValue;

                return [

                    'sku' => $item->sku,

                    'product' => $item->product_name,

                    'category' => $item->category->category_name ?? '',

                    'brand' => $item->brand->brand_name ?? '',

                    'stock' => $item->stock,

                    'average_cost' => $item->average_cost,
                                        'cost_value' => $costValue,

                    'selling_price' => $item->sell_price,

                    'selling_value' => $sellingValue,

                    'gross_profit' => $grossProfit,

                    'status' => $this->service->stockStatus($item),

                ];

            });
    }

    public function headings(): array
    {
        return [

            'SKU',

            'Product',

            'Category',

            'Brand',

            'Stock Qty',

            'Average Cost',

            'Cost Value',

            'Selling Price',

            'Selling Value',

            'Gross Profit',

            'Stock Status',

        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            1 => [

                'font' => [
                    'bold' => true,
                ],

                'alignment' => [
                    'horizontal' => 'center',
                ],

            ],

        ];
    }
    }