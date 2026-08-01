<?php

namespace App\Services\Finance;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SalesReturn;
use Illuminate\Database\Eloquent\Collection;

class FinancialSummaryService
{
    /**
     * Total sales revenue.
     */
    public function salesRevenue(array $filters = []): float
    {
        return round(
            (float) $this->salesQuery($filters)
                ->sum('grand_total'),
            2
        );
    }

    /**
     * Sales returns.
     *
     * Sales Return table stores subtotal only.
     */
    public function salesReturns(array $filters = []): float
    {
        return round(
            (float) $this->salesReturnsQuery($filters)
                ->sum('subtotal'),
            2
        );
    }

    /**
     * Net sales.
     */
    public function netSales(array $filters = []): float
    {
        return round(
            $this->salesRevenue($filters)
            - $this->salesReturns($filters),
            2
        );
    }

    /**
     * Purchase amount.
     */
    public function purchaseAmount(array $filters = []): float
    {
        return round(
            (float) $this->purchaseQuery($filters)
                ->sum('grand_total'),
            2
        );
    }

    /**
     * Purchase Returns.
     *
     * Purchase Return table stores subtotal only.
     */
    public function purchaseReturns(array $filters = []): float
    {
        return round(
            (float) $this->purchaseReturnsQuery($filters)
                ->sum('subtotal'),
            2
        );
    }

    /**
     * Net Purchases.
     */
    public function netPurchases(array $filters = []): float
    {
        return round(
            $this->purchaseAmount($filters)
            - $this->purchaseReturns($filters),
            2
        );
    }

    /**
     * Inventory valuation.
     *
     * Sum(Product Stock × Average Cost)
     */
    public function inventoryValue(): float
    {
        return round(
            (float) Product::query()
                ->selectRaw(
                    'COALESCE(SUM(stock * average_cost),0) as total'
                )
                ->value('total'),
            2
        );
    }

    /**
     * Cost Of Goods Sold.
     */
    public function costOfGoodsSold(array $filters = []): float
    {
        $sales = $this->salesQuery($filters)
            ->with('details')
            ->get();

        return round(
            $sales->sum(function (Sale $sale) {

                return $sale->details->sum(function ($detail) {

                    return
                        ((float) $detail->unit_cost)
                        *
                        ((float) $detail->qty);

                });

            }),
            2
        );
    }

    /**
     * Gross Profit.
     */
    public function grossProfit(array $filters = []): float
    {
        return round(
            $this->netSales($filters)
            - $this->costOfGoodsSold($filters),
            2
        );
    }

    /**
     * Net Profit.
     *
     * Future:
     * Operating Expense
     * Salary
     * Rent
     * Utility
     * Tax
     */
    public function netProfit(array $filters = []): float
    {
        return $this->grossProfit($filters);
    }

    /**
     * Cash Received.
     */
    public function cashBalance(array $filters = []): float
    {
        return round(
            (float) $this->salesQuery($filters)
                ->sum('paid_amount'),
            2
        );
    }

    /**
     * Accounts Receivable.
     */
    public function accountsReceivable(array $filters = []): float
    {
        return round(

            $this->salesQuery($filters)
                ->get()
                ->sum(function (Sale $sale) {

                    return max(
                        0,
                        (float) $sale->grand_total
                        - (float) $sale->paid_amount
                    );

                }),

            2

        );
    }

    /**
     * Accounts Payable.
     */
    public function accountsPayable(array $filters = []): float
    {
        return round(

            $this->purchaseQuery($filters)
                ->get()
                ->sum(function (Purchase $purchase) {

                    return max(
                        0,
                        (float) $purchase->grand_total
                        - (float) $purchase->paid_amount
                    );

                }),

            2

        );
    }

    // ===== PART 2 =====
    /**
     * Dashboard Summary.
     */
    public function dashboard(array $filters = []): array
    {
        $salesRevenue = $this->salesRevenue($filters);
        $salesReturns = $this->salesReturns($filters);
        $netSales = $salesRevenue - $salesReturns;

        $purchaseAmount = $this->purchaseAmount($filters);
        $purchaseReturns = $this->purchaseReturns($filters);
        $netPurchases = $purchaseAmount - $purchaseReturns;

        $cogs = $this->costOfGoodsSold($filters);

        $grossProfit = $netSales - $cogs;

        return [

            'sales_revenue' => round($salesRevenue, 2),

            'sales_returns' => round($salesReturns, 2),

            'net_sales' => round($netSales, 2),

            'purchase_amount' => round($purchaseAmount, 2),

            'purchase_returns' => round($purchaseReturns, 2),

            'net_purchases' => round($netPurchases, 2),

            'inventory_value' => $this->inventoryValue(),

            'cost_of_goods_sold' => round($cogs, 2),

            'gross_profit' => round($grossProfit, 2),

            'net_profit' => round($this->netProfit($filters), 2),

            'cash_balance' => $this->cashBalance($filters),

            'accounts_receivable' => $this->accountsReceivable($filters),

            'accounts_payable' => $this->accountsPayable($filters),

        ];
    }
        /**
     * Backward-compatible dashboard summary.
     *
     * This alias preserves compatibility with services that still
     * call dashboardSummary() while the primary implementation
     * remains dashboard().
     */
    public function dashboardSummary(array $filters = []): array
    {
        return $this->dashboard($filters);
    }
    /**
     * Dashboard cards.
     */
    public function summary(array $filters = []): array
    {
        return [
            'sales' => $this->salesRevenue($filters),
            'purchases' => $this->purchaseAmount($filters),
            'profit' => $this->grossProfit($filters),
            'inventory' => $this->inventoryValue(),
            'receivable' => $this->accountsReceivable($filters),
            'payable' => $this->accountsPayable($filters),
        ];
    }

    /**
     * Sales query.
     */
    protected function salesQuery(array $filters = [])
    {
        $query = Sale::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Purchase query.
     */
    protected function purchaseQuery(array $filters = [])
    {
        $query = Purchase::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('purchase_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('purchase_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Purchase Return query.
     */
    protected function purchaseReturnsQuery(array $filters = [])
    {
        $query = PurchaseReturn::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('return_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('return_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    // ===== PART 3 =====
    /**
     * Sales Return query.
     */
    protected function salesReturnsQuery(array $filters = [])
    {
        $query = SalesReturn::query();

        if (!empty($filters['date_from'])) {
            $query->whereDate('return_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('return_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Products with stock.
     */
    public function productsInStock(): Collection
    {
        return Product::query()
            ->where('stock', '>', 0)
            ->orderBy('product_name')
            ->get();
    }

    /**
     * Low stock products.
     */
    public function lowStockProducts(): Collection
    {
        return Product::query()
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->get();
    }

    /**
     * Total stock quantity.
     */
    public function totalStock(): float
    {
        return round(
            (float) Product::query()->sum('stock'),
            2
        );
    }

    /**
     * Total SKU.
     */
    public function totalProducts(): int
    {
        return Product::query()->count();
    }

    /**
     * Average inventory cost.
     */
    public function averageInventoryCost(): float
    {
        $products = Product::query()
            ->where('stock', '>', 0)
            ->get();

        if ($products->isEmpty()) {
            return 0;
        }

        return round(
            $products->avg('average_cost'),
            2
        );
    }

    /**
     * Inventory statistics.
     */
    public function inventoryStatistics(): array
    {
        return [
            'total_products'      => $this->totalProducts(),
            'total_stock'         => $this->totalStock(),
            'inventory_value'     => $this->inventoryValue(),
            'average_cost'        => $this->averageInventoryCost(),
            'low_stock_products'  => $this->lowStockProducts()->count(),
        ];
    }
}