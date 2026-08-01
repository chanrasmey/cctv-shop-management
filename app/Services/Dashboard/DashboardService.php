<?php

namespace App\Services\Dashboard;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Finance\FinancialSummaryService;

class DashboardService
{
    public function __construct(
        protected FinancialSummaryService $financial
    ) {
    }
    /**
     * Dashboard statistics.
     */
    public function statistics(): array
    {
        return [

            'products' => Product::count(),

            'categories' => Category::count(),

            'brands' => Brand::count(),

            'customers' => Customer::count(),

            'suppliers' => Supplier::count(),

        ];
    }
    /**
     * Dashboard data.
     */
    public function dashboard(): array
    {
        return [

            'statistics' => $this->statistics(),
            
            'summary' => $this->financial->dashboardSummary(),

            'today_sales' => $this->todaySales(),

            'month_sales' => $this->monthSales(),

            'today_profit' => $this->todayProfit(),

            'month_profit' => $this->monthProfit(),
            
            'inventory_value' => $this->financial->inventoryValue(),

            'low_stock_products' => $this->lowStockProducts(),

            'top_selling_products' => $this->topSellingProducts(),

            'recent_sales' => $this->recentSales(),
        ];
    }

       /**
     * Products below minimum stock.
     */
    public function lowStockProducts(int $limit = 10)
    {
        return Product::query()
            ->lowStock()
            ->with(['category', 'brand', 'unit'])
            ->latestFirst()
            ->limit($limit)
            ->get();
    }

       /**
     * Recent completed sales.
     */
    public function recentSales(int $limit = 10)
    {
        return Sale::query()
            ->with([
                'customer',
                'details',
            ])
            ->latestFirst()
            ->limit($limit)
            ->get();
    }

       /**
     * Top selling products.
     */
    public function topSellingProducts(int $limit = 10)
    {
        return Product::query()
            ->with([
                'category',
                'brand',
                'unit',
            ])
            ->withSum('saleDetails', 'qty')
            ->orderByDesc('sale_details_sum_qty')
            ->limit($limit)
            ->get();
    }
        /**
     * Today's sales amount.
     */
    public function todaySales(): float
    {
        return $this->financial->salesRevenue([
            'date_from' => today()->toDateString(),
            'date_to'   => today()->toDateString(),
        ]);
    }

    /**
     * Today's net profit.
     */
    public function todayProfit(): float
    {
        return $this->financial->netProfit([
            'date_from' => today()->toDateString(),
            'date_to'   => today()->toDateString(),
        ]);
    }

    /**
     * Current month's sales amount.
     */
    public function monthSales(): float
    {
        return $this->financial->salesRevenue([
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->endOfMonth()->toDateString(),
        ]);
    }

    /**
     * Current month's net profit.
     */
    public function monthProfit(): float
    {
        return $this->financial->netProfit([
            'date_from' => now()->startOfMonth()->toDateString(),
            'date_to'   => now()->endOfMonth()->toDateString(),
        ]);
    }
}