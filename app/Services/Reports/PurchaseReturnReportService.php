<?php

namespace App\Services\Reports;

use App\Models\CompanySetting;
use App\Models\PurchaseReturn;
use App\Models\Supplier;

class PurchaseReturnReportService
{
    public function returns(array $filters = [])
    {
        return $this->query($filters)
            ->latest('return_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();
    }

    public function returnsForPrint(array $filters = [])
    {
        return $this->query($filters)
            ->latest('return_date')
            ->latest('id')
            ->get();
    }

    protected function query(array $filters = [])
    {
        return PurchaseReturn::with([
            'supplier',
            'purchase',
            'details.product',
        ])

        ->when(
            !empty($filters['date_from']),
            fn ($q) => $q->whereDate(
                'return_date',
                '>=',
                $filters['date_from']
            )
        )

        ->when(
            !empty($filters['date_to']),
            fn ($q) => $q->whereDate(
                'return_date',
                '<=',
                $filters['date_to']
            )
        )

        ->when(
            !empty($filters['supplier_id']),
            fn ($q) => $q->where(
                'supplier_id',
                $filters['supplier_id']
            )
        )

        ->when(
            !empty($filters['status']),
            fn ($q) => $q->where(
                'status',
                $filters['status']
            )
        );
    }

    public function summary(array $filters = []): array
    {
        $returns = $this->query($filters)->get();

        return [
            'returns' => $returns->count(),
            'items' => $returns->sum(
                fn ($return) => $return->details->sum('qty')
            ),
            'subtotal' => $returns->sum('subtotal'),
            'discount' => $returns->sum('discount_amount'),
            'tax' => $returns->sum('tax_amount'),
            'grand_total' => $returns->sum('grand_total'),
            'refund' => $returns->sum('refund_amount'),
        ];
    }

    public function suppliers()
    {
        return Supplier::orderBy('company_name')->get();
    }

    public function statuses(): array
    {
        return [
            'Draft',
            'Pending',
            'Completed',
            'Cancelled',
        ];
    }

    public function company()
    {
        return CompanySetting::first();
    }
}