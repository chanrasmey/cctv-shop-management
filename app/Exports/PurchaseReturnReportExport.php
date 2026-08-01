<?php

namespace App\Exports;

use App\Models\CompanySetting;
use App\Models\PurchaseReturn;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PurchaseReturnReportExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(string $filename = 'purchase-return-report.xlsx')
    {
        $spreadsheet = $this->buildSpreadsheet();

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function buildSpreadsheet(): Spreadsheet
    {
        $company = CompanySetting::first();

        $returns = $this->query()->get();

        $sheet = (new Spreadsheet())->getActiveSheet();

        $row = 1;

        $sheet->setCellValue("A{$row}", $company->company_name ?? config('app.name'));
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(16);

        $row++;

        $sheet->setCellValue("A{$row}", 'Purchase Return Report');
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13);

        $row += 2;

        $sheet->setCellValue("A{$row}", 'Date From');
        $sheet->setCellValue("B{$row}", $this->filters['date_from'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Date To');
        $sheet->setCellValue("B{$row}", $this->filters['date_to'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Supplier');
        $sheet->setCellValue("B{$row}", $this->filters['supplier_name'] ?? 'All');

        $row++;

        $sheet->setCellValue("A{$row}", 'Status');
        $sheet->setCellValue("B{$row}", $this->filters['status'] ?? 'All');

        $row += 2;

        $headers = [
            'Date',
            'Return No',
            'Purchase No',
            'Supplier',
            'Qty',
            'Grand Total',
            'Refund',
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

        foreach ($returns as $return) {

            $sheet->setCellValue("A{$row}", optional($return->return_date)->format('Y-m-d'));
            $sheet->setCellValue("B{$row}", $return->return_no);
            $sheet->setCellValue("C{$row}", $return->purchase->purchase_no ?? '-');
            $sheet->setCellValue("D{$row}", $return->supplier->company_name ?? '-');
            $sheet->setCellValue("E{$row}", $return->details->sum('qty'));
            $sheet->setCellValue("F{$row}", (float) $return->grand_total);
            $sheet->setCellValue("G{$row}", (float) $return->refund_amount);
            $sheet->setCellValue("H{$row}", $return->status);

            $sheet->getStyle("A{$row}:H{$row}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $sheet->getStyle("F{$row}:G{$row}")
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
                "G{$row}",
                "=SUM(G{$startDataRow}:G{$endDataRow})"
            );

        } else {

            $sheet->setCellValue("F{$row}", 0);
            $sheet->setCellValue("G{$row}", 0);

        }

        $sheet->getStyle("F{$row}:G{$row}")
            ->getFont()
            ->setBold(true);

        $sheet->getStyle("F{$row}:G{$row}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $sheet->getStyle("E{$row}:H{$row}")
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

        foreach (range('A', 'H') as $column) {
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
        return PurchaseReturn::query()
            ->with([
                'purchase',
                'supplier',
                'details',
            ])
            ->when(
                !empty($this->filters['date_from']),
                fn ($query) => $query->whereDate(
                    'return_date',
                    '>=',
                    $this->filters['date_from']
                )
            )
            ->when(
                !empty($this->filters['date_to']),
                fn ($query) => $query->whereDate(
                    'return_date',
                    '<=',
                    $this->filters['date_to']
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
                !empty($this->filters['status']),
                fn ($query) => $query->where(
                    'status',
                    $this->filters['status']
                )
            )
            ->orderByDesc('return_date')
            ->orderByDesc('id');
    }
}