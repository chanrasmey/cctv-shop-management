<?php

namespace App\Services\Reports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Models\Supplier;

class InventoryReportService
{
    public function products(array $filters = [])
    {
        return $this->query($filters)
            ->paginate(25)
            ->withQueryString();
    }

    public function productsForPrint(array $filters = [])
    {
        return $this->query($filters)->get();
    }

    public function summary(array $filters = []): array
    {
        $products = $this->query($filters)->get();

        return [
            'products' => $products->count(),
            'stock_qty' => $products->sum('stock'),
            'inventory_cost' => $products->sum(
                fn ($product) => $product->stock * $product->average_cost
            ),
            'inventory_sale' => $products->sum(
                fn ($product) => $product->stock * $product->sell_price
            ),
            'low_stock' => $products->filter(function ($product) {
                return $product->stock <= ($product->minimum_stock ?? 0);
            })->count(),
            'out_of_stock' => $products->where('stock', '<=', 0)->count(),
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

            ->when(
                !empty($filters['supplier_id']),
                fn ($query) => $query->where(
                    'supplier_id',
                    $filters['supplier_id']
                )
            )

            ->when(
                !empty($filters['keyword']),
                function ($query) use ($filters) {

                    $keyword = trim($filters['keyword']);

                    $query->where(function ($q) use ($keyword) {

                        $q->where('product_name', 'like', "%{$keyword}%")
                          ->orWhere('sku', 'like', "%{$keyword}%")
                          ->orWhere('barcode', 'like', "%{$keyword}%");

                    });

                }
            )
                        ->when(
                !empty($filters['stock_status']),
                function ($query) use ($filters) {

                    switch ($filters['stock_status']) {

                        case 'in_stock':
                            $query->where('stock', '>', 0);
                            break;

                        case 'low_stock':
                            $query->whereColumn('stock', '<=', 'minimum_stock')
                                  ->where('stock', '>', 0);
                            break;

                        case 'out_of_stock':
                            $query->where('stock', '<=', 0);
                            break;

                    }

                }
            )

            ->orderBy('product_name');
    }

    public function categories()
    {
        return Category::orderBy('category_name')->get();
    }

    public function brands()
    {
        return Brand::orderBy('brand_name')->get();
    }

    public function suppliers()
    {
        return Supplier::orderBy('company_name')->get();
    }

    public function company()
    {
        return CompanySetting::first();
    }
        /**
     * Get stock status label for a product.
     */
    public function stockStatus(Product $product): string
    {
        if ($product->stock <= 0) {
            return 'Out of Stock';
        }

        if ($product->stock <= ($product->minimum_stock ?? 0)) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    /**
     * Get stock status badge color.
     */
    public function stockStatusColor(Product $product): string
    {
        if ($product->stock <= 0) {
            return 'danger';
        }

        if ($product->stock <= ($product->minimum_stock ?? 0)) {
            return 'warning';
        }

        return 'success';
    }
}