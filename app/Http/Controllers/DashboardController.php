<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(DashboardService $dashboardService): View
    {
        $dashboard = $dashboardService->dashboard();

        return view('dashboard', [
            'dashboard'          => $dashboard,

            // Summary
            'summary'            => $dashboard['summary'] ?? [],

            // Statistics
            'statistics'         => $dashboard['statistics'] ?? [],
            'products'           => $dashboard['statistics']['products'] ?? 0,
            'categories'         => $dashboard['statistics']['categories'] ?? 0,
            'brands'             => $dashboard['statistics']['brands'] ?? 0,
            'customers'          => $dashboard['statistics']['customers'] ?? 0,
            'suppliers'          => $dashboard['statistics']['suppliers'] ?? 0,

            // Financial KPIs
            'todaySales'         => $dashboard['today_sales'] ?? 0,
            'monthSales'         => $dashboard['month_sales'] ?? 0,
            'todayProfit'        => $dashboard['today_profit'] ?? 0,
            'monthProfit'        => $dashboard['month_profit'] ?? 0,
            'inventoryValue'     => $dashboard['inventory_value'] ?? 0,

            // Dashboard Tables
            'lowStockProducts'   => $dashboard['low_stock_products'] ?? collect(),
            'topSellingProducts' => $dashboard['top_selling_products'] ?? collect(),
            'recentSales'        => $dashboard['recent_sales'] ?? collect(),
        ]);
    }
}