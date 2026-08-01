<?php

namespace App\Services\Reports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;

class StockReportService
{
    public function products(array $filters = [])
    {
        return Product::query()
            ->with(['category', 'brand', 'unit'])
            ->when(
                !empty($filters['category_id']),
                fn($q) => $q->where('category_id', $filters['category_id'])
            )
            ->when(
                !empty($filters['brand_id']),
                fn($q) => $q->where('brand_id', $filters['brand_id'])
            )
            ->when(
                !empty($filters['search']),
                function ($q) use ($filters) {
                    $search = trim($filters['search']);

                    $q->where(function ($query) use ($search) {
                        $query->where('product_name', 'like', "%{$search}%")
                              ->orWhere('sku', 'like', "%{$search}%")
                              ->orWhere('barcode', 'like', "%{$search}%");
                    });
                }
            )
            ->orderBy('product_name')
            ->paginate(25)
            ->withQueryString();
    }

    public function summary(array $filters = [])
    {
        $query = Product::query()
            ->when(
                !empty($filters['category_id']),
                fn($q) => $q->where('category_id', $filters['category_id'])
            )
            ->when(
                !empty($filters['brand_id']),
                fn($q) => $q->where('brand_id', $filters['brand_id'])
            );

        $products = $query->get();

        return [
            'products'    => $products->count(),
            'stock_qty'   => $products->sum('stock'),
            'stock_value' => $products->sum(fn($item) => $item->stock * $item->average_cost),
        ];
    }

    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    public function brands()
    {
        return Brand::orderBy('name')->get();
    }

    public function company()
    {
        return CompanySetting::first();
    }
}