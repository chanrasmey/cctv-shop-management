<?php

namespace App\Services\Reports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;

class StockCardReportService
{
    public function movements(array $filters = [])
    {
        return $this->query($filters)
            ->paginate(50)
            ->withQueryString();
    }

    public function movementsForPrint(array $filters = [])
    {
        return $this->query($filters)->get();
    }

    public function summary(array $filters = []): array
    {
        $movements = $this->query($filters)->get();

        $opening = 0;

        $stockIn = $movements
            ->where('movement_type', 'IN')
            ->sum('quantity');

        $stockOut = $movements
            ->where('movement_type', 'OUT')
            ->sum('quantity');

        $balance = $opening + $stockIn - $stockOut;

        return [
            'opening' => $opening,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
            'closing' => $balance,
            'movements' => $movements->count(),
        ];
    }

    protected function query(array $filters = [])
    {
        return StockMovement::query()

            ->with([
                'product.category',
                'product.brand',
                'product.unit',
                'product.supplier',
                'createdBy',
            ])

            ->when(
                !empty($filters['product_id']),
                fn ($query) => $query->where(
                    'product_id',
                    $filters['product_id']
                )
            )

            ->when(
                !empty($filters['category_id']),
                function ($query) use ($filters) {

                    $query->whereHas('product', function ($q) use ($filters) {

                        $q->where(
                            'category_id',
                            $filters['category_id']
                        );

                    });

                }
            )

            ->when(
                !empty($filters['brand_id']),
                function ($query) use ($filters) {

                    $query->whereHas('product', function ($q) use ($filters) {

                        $q->where(
                            'brand_id',
                            $filters['brand_id']
                        );

                    });

                }
            )

            ->when(
                !empty($filters['supplier_id']),
                function ($query) use ($filters) {

                    $query->whereHas('product', function ($q) use ($filters) {

                        $q->where(
                            'supplier_id',
                            $filters['supplier_id']
                        );

                    });

                }
            )
                        ->when(
                !empty($filters['date_from']),
                fn ($query) => $query->whereDate(
                    'movement_date',
                    '>=',
                    $filters['date_from']
                )
            )

            ->when(
                !empty($filters['date_to']),
                fn ($query) => $query->whereDate(
                    'movement_date',
                    '<=',
                    $filters['date_to']
                )
            )

            ->orderBy('movement_date')
            ->orderBy('id');
    }

    public function products()
    {
        return Product::orderBy('product_name')->get();
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
     * Calculate running balance for stock movements.
     */
    public function runningBalance($movements, int|float $opening = 0)
    {
        $balance = $opening;

        return $movements->map(function ($movement) use (&$balance) {

            $qty = (float) $movement->quantity;

            if ($movement->movement_type === 'IN') {
                $balance += $qty;
            } else {
                $balance -= $qty;
            }

            $movement->running_balance = $balance;

            return $movement;
        });
    }
    public function movements(array $filters = [])
{
    $paginator = $this->query($filters)
        ->paginate(50)
        ->withQueryString();

    $paginator->setCollection(
        $this->runningBalance(
            $paginator->getCollection(),
            $this->openingBalance($filters)
        )
    );

    return $paginator;
}

public function movementsForPrint(array $filters = [])
{
    return $this->runningBalance(
        $this->query($filters)->get(),
        $this->openingBalance($filters)
    );
}

/**
 * Calculate opening balance before the selected period.
 */
protected function openingBalance(array $filters = []): float
{
    if (empty($filters['product_id'])) {
        return 0;
    }

    $query = StockMovement::query()
        ->where('product_id', $filters['product_id']);

    if (!empty($filters['date_from'])) {
        $query->whereDate('movement_date', '<', $filters['date_from']);
    }

    $opening = 0;

    foreach ($query->orderBy('movement_date')->orderBy('id')->get() as $movement) {

        if ($movement->movement_type === 'IN') {
            $opening += $movement->quantity;
        } else {
            $opening -= $movement->quantity;
        }
    }

    return $opening;
}
}