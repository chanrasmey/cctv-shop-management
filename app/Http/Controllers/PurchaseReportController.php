<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'supplier_id' => $request->input('supplier_id'),
            'status' => $request->input('status'),
        ];

        $purchasesQuery = Purchase::query()
            ->with([
                'supplier',
                'returns' => function (Builder $query): void {
                    $query->where('status', 'Completed');
                },
            ])
            ->when($filters['date_from'], function (Builder $query, string $date): Builder {
                return $query->whereDate('purchase_date', '>=', $date);
            })
            ->when($filters['date_to'], function (Builder $query, string $date): Builder {
                return $query->whereDate('purchase_date', '<=', $date);
            })
            ->when($filters['supplier_id'], function (Builder $query, string $supplierId): Builder {
                return $query->where('supplier_id', $supplierId);
            })
            ->when($filters['status'], function (Builder $query, string $status): Builder {
                return $query->where('status', $status);
            });

        $purchasesForTotals = (clone $purchasesQuery)->get();

        $returnTotal = $purchasesForTotals->sum(
            fn (Purchase $purchase): float => $this->purchaseReturnTotal($purchase)
        );

        $grandTotal = (float) $purchasesForTotals->sum('grand_total');

        $summary = [
            'purchase_count' => $purchasesForTotals->count(),
            'subtotal' => (float) $purchasesForTotals->sum('subtotal'),
            'discount_amount' => (float) $purchasesForTotals->sum('discount_amount'),
            'tax_amount' => (float) $purchasesForTotals->sum('tax_amount'),
            'grand_total' => $grandTotal,
            'paid_amount' => (float) $purchasesForTotals->sum('paid_amount'),
            'balance' => (float) $purchasesForTotals->sum('balance'),
            'return_total' => (float) $returnTotal,
            'net_total' => $grandTotal - (float) $returnTotal,
        ];

        $purchases = (clone $purchasesQuery)
            ->latest('purchase_date')
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        $suppliers = Supplier::orderBy('company_name')->get();
        $statusOptions = ['Draft', 'Pending', 'Completed', 'Cancelled'];

        return view('reports.purchases', compact(
            'filters',
            'purchases',
            'suppliers',
            'statusOptions',
            'summary'
        ));
    }

    private function purchaseReturnTotal(Purchase $purchase): float
    {
        return (float) $purchase->returns->sum(
            fn ($purchaseReturn): float => (float) $purchaseReturn->subtotal
        );
    }
}
