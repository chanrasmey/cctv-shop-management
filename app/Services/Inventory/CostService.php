<?php

namespace App\Services\Inventory;

use App\Models\Product;

class CostService
{
    /**
     * Update product cost after a purchase.
     *
     * buy_price     = Latest purchase price
     * average_cost = Weighted Average Cost (WAC)
     */
    public function updatePurchaseCost(
        Product $product,
        float $purchaseQty,
        float $unitCost
    ): void {

        $currentStock = (float) $product->stock;
        $currentAverageCost = (float) ($product->average_cost ?? 0);

        /*
        |--------------------------------------------------------------------------
        | Weighted Average Cost
        |--------------------------------------------------------------------------
        |
        | Formula:
        |
        | ((Current Stock × Current Average Cost)
        |      +
        | (Purchase Qty × Purchase Cost))
        |
        | -------------------------------
        | Current Stock + Purchase Qty
        |
        */

        if ($currentStock <= 0) {

            $averageCost = $unitCost;

        } else {

            $averageCost = (
                ($currentStock * $currentAverageCost)
                +
                ($purchaseQty * $unitCost)
            ) / ($currentStock + $purchaseQty);

        }

        $product->update([

            'buy_price'     => round($unitCost, 2),

            'average_cost'  => round($averageCost, 2),

        ]);
    }

    /**
     * Recalculate average cost after reversing
     * a purchase or deleting purchase details.
     *
     * For now we simply keep the current
     * average cost if stock still exists.
     *
     * Later this will be upgraded to
     * calculate directly from stock ledger.
     */
    public function recalculateAverageCost(Product $product): void
    {
        if ($product->stock <= 0) {

            $product->update([

                'average_cost' => 0,

            ]);

        }
    }

    /**
     * Latest purchase cost.
     */
    public function getCurrentCost(Product $product): float
    {
        return (float) $product->buy_price;
    }

    /**
     * Average inventory cost.
     */
    public function getAverageCost(Product $product): float
    {
        return (float) ($product->average_cost ?? 0);
    }

    /**
     * Inventory valuation.
     */
    public function getInventoryValue(Product $product): float
    {
        return round(

            (float) $product->stock
            *
            (float) ($product->average_cost ?: $product->buy_price),

            2

        );
    }

    /**
     * Expected gross profit.
     */
    public function getEstimatedProfit(Product $product): float
    {
        return round(

            (float) $product->sell_price
            -
            (float) ($product->average_cost ?: $product->buy_price),

            2

        );
    }
}