<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Increase product stock after purchase.
     */
    public function stockIn(
        Product $product,
        float $qty,
        float $unitCost,
        string $referenceNo,
        int $referenceId
    ): void {

        // Update Product Stock

        $product->increment('stock', $qty);

        // Refresh product value

        $product->refresh();

        // Create Stock Movement

        StockMovement::create([

            'product_id'     => $product->id,

            'movement_type'  => 'Purchase',

            'reference_no'   => $referenceNo,

            'reference_id'   => $referenceId,

            'qty_in'         => $qty,

            'qty_out'        => 0,

            'balance'        => $product->stock,

            'unit_cost'      => $unitCost,

            'remark'         => 'Purchase Stock In',

            'created_by'     => Auth::id(),

        ]);
    }

    /**
     * Reverse stock received from a purchase.
     */
    public function purchaseReturn(
        Product $product,
        float $qty,
        float $unitCost,
        string $referenceNo,
        int $referenceId,
        string $remark = 'Purchase Cancelled'
    ): void {
        $product->decrement('stock', $qty);

        $product->refresh();

        StockMovement::create([

            'product_id'     => $product->id,

            'movement_type'  => 'Purchase Return',

            'reference_no'   => $referenceNo,

            'reference_id'   => $referenceId,

            'qty_in'         => 0,

            'qty_out'        => $qty,

            'balance'        => $product->stock,

            'unit_cost'      => $unitCost,

            'remark'         => $remark,

            'created_by'     => Auth::id(),

        ]);
    }

    /**
     * Reduce stock after sale.
     */
    public function stockOut(
        Product $product,
        float $qty,
        string $referenceNo,
        int $referenceId,
        ?float $unitCost = null,
        string $remark = 'Sales Stock Out'
    ): void {

        $product->decrement('stock', $qty);

        $product->refresh();

        StockMovement::create([

            'product_id'     => $product->id,

            'movement_type'  => 'Sale',

            'reference_no'   => $referenceNo,

            'reference_id'   => $referenceId,

            'qty_in'         => 0,

            'qty_out'        => $qty,

            'balance'        => $product->stock,

            'unit_cost'      => $unitCost ?? (float) ($product->average_cost ?: $product->buy_price),

            'remark'         => $remark,

            'created_by'     => Auth::id(),

        ]);
    }
}
