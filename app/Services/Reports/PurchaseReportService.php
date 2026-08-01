<?php

namespace App\Services\Reports;

use App\Models\CompanySetting;
use App\Models\Purchase;
use App\Models\Supplier;

class PurchaseReportService
{
    public function purchases(array $filters = [])
    {
        return $this->query($filters)
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();
    }

    public function purchasesForPrint(array $filters = [])
    {
        return $this->query($filters)
            ->latest('purchase_date')
            ->latest('id')
            ->get();
    }

    protected function query(array $filters = [])
    {
        return Purchase::with([
            'supplier',
            'returns',
            'details.product',
        ])

        ->when(
            !empty($filters['date_from']),
            fn ($q) => $q->whereDate(
                'purchase_date',
                '>=',
                $filters['date_from']
            )
        )

        ->when(
            !empty($filters['date_to']),
            fn ($q) => $q->whereDate(
                'purchase_date',
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
        $purchases = $this->query($filters)->get();

        $returnTotal = $purchases->sum(function ($purchase) {
            return $purchase->returns
                ->where('status', 'Completed')
                ->sum('subtotal');
        });

        $grandTotal = $purchases->sum('grand_total');

        return [
            'purchase_count' => $purchases->count(),
            'subtotal'       => $purchases->sum('subtotal'),
            'discount'       => $purchases->sum('discount_amount'),
            'tax'            => $purchases->sum('tax_amount'),
            'grand_total'    => $grandTotal,
            'paid'           => $purchases->sum('paid_amount'),
            'balance'        => $purchases->sum('balance'),
            'return_total'   => $returnTotal,
            'net_total'      => $grandTotal - $returnTotal,
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