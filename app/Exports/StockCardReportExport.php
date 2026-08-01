<?php

namespace App\Exports;

use App\Services\Reports\StockCardReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockCardReportExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    protected array $filters;

    protected StockCardReportService $service;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->service = app(StockCardReportService::class);
    }

    public function collection()
    {
        return $this->service
            ->movementsForPrint($this->filters)
            ->map(function ($movement) {

                $inQty = $movement->movement_type === 'IN'
                    ? $movement->quantity
                    : 0;

                $outQty = $movement->movement_type === 'OUT'
                    ? $movement->quantity
                    : 0;

                return [

                    'date' => optional($movement->movement_date)
                        ->format('Y-m-d'),

                    'product' => $movement->product->product_name ?? '',

                    'reference' => $movement->reference_no,

                    'type' => $movement->movement_type,

                    'stock_in' => $inQty,

                    'stock_out' => $outQty,
                                        'balance' => $movement->running_balance,

                    'remark' => $movement->remark,

                    'user' => $movement->createdBy->name ?? '',

                ];
            });
    }

    public function headings(): array
    {
        return [

            'Date',

            'Product',

            'Reference',

            'Movement Type',

            'Stock In',

            'Stock Out',

            'Running Balance',

            'Remark',

            'Created By',

        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            1 => [

                'font' => [
                    'bold' => true,
                ],

            ],

        ];
    }
    }