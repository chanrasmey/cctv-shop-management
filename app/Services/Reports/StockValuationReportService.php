<?php

namespace App\Services\Reports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;

class StockValuationReportService
{
    public function items(array $filters = [])
    {
        return $this->query($filters)
            ->paginate(50)
            ->withQueryString();
    }

    public function itemsForPrint(array $filters = [])
    {
        return $this->query($filters)->get();
    }

    public function summary(array $filters = [])
    {
        $items = $this->query($filters)->get();

        $totalQty = $items->sum('stock');

        $totalCostValue = $items->sum(function ($item) {
            return $item->stock * $item->average_cost;
        });

        $totalSellValue = $items->sum(function ($item) {
            return $item->stock * $item->sell_price;
        });

        return [

            'total_products' => $items->count(),

            'total_qty' => $totalQty,

            'cost_value' => $totalCostValue,

            'selling_value' => $totalSellValue,

            'gross_profit' => $totalSellValue - $totalCostValue,

        ];
    }

    protected function query(array $filters = [])
    {
        return Product::query()

            ->with([
                'category',
                'brand',
                'unit',
                'supplier',
            ])

            ->when(
                !empty($filters['category_id']),
                fn ($query) => $query->where(
                    'category_id',
                    $filters['category_id']
                )
            )

            ->when(
                !empty($filters['brand_id']),
                fn ($query) => $query->where(
                    'brand_id',
                    $filters['brand_id']
                )
            )

            ->orderBy('product_name');
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

    public function categorySummary(array $filters = [])
    {
        return $this->query($filters)
            ->get()
            ->groupBy(fn ($item) => optional($item->category)->category_name ?? 'Uncategorized')
            ->map(function ($items, $category) {

                $qty = $items->sum('stock');

                $costValue = $items->sum(function ($item) {
                    return $item->stock * $item->average_cost;
                });

                $sellValue = $items->sum(function ($item) {
                    return $item->stock * $item->sell_price;
                });

                return [
                    'category'      => $category,
                    'products'      => $items->count(),
                    'qty'           => $qty,
                    'cost_value'    => $costValue,
                    'selling_value' => $sellValue,
                    'gross_profit'  => $sellValue - $costValue,
                ];
            })
            ->values();
    }

    public function brandSummary(array $filters = [])
    {
        return $this->query($filters)
            ->get()
            ->groupBy(fn ($item) => optional($item->brand)->brand_name ?? 'No Brand')
            ->map(function ($items, $brand) {

                $qty = $items->sum('stock');

                $costValue = $items->sum(function ($item) {
                    return $item->stock * $item->average_cost;
                });

                $sellValue = $items->sum(function ($item) {
                    return $item->stock * $item->sell_price;
                });

                return [
                    'brand'         => $brand,
                    'products'      => $items->count(),
                    'qty'           => $qty,
                    'cost_value'    => $costValue,
                    'selling_value' => $sellValue,
                    'gross_profit'  => $sellValue - $costValue,
                ];
            })
            ->values();
    }
        public function stockStatus(Product $product): string
    {
        if ($product->stock <= 0) {
            return 'Out of Stock';
        }

        if (
            !is_null($product->minimum_stock)
            && $product->stock <= $product->minimum_stock
        ) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    public function stockStatusColor(Product $product): string
    {
        return match ($this->stockStatus($product)) {
            'Out of Stock' => 'danger',
            'Low Stock'    => 'warning',
            default         => 'success',
        };
    }

    public function lowStockItems(array $filters = [])
    {
        return $this->query($filters)
            ->get()
            ->filter(function ($product) {
                return !is_null($product->minimum_stock)
                    && $product->stock <= $product->minimum_stock;
            })
            ->values();
    }
}
