<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Increase inventory.
     *
     * Used by:
     * - Purchase
     * - Sales Return
     * - Opening Balance
     * - Stock Adjustment (+)
     */
    public function stockIn(
        Product $product,
        float $quantity,
        string $referenceNo,
        int $referenceId,
        ?float $unitCost = null,
        string $movementType = 'Purchase',
        string $remark = 'Stock In'
    ): void {

        $quantity = $this->normalize($quantity);

	$this->validateQuantity($quantity);

        $unitCost ??= (float) (
            $product->average_cost
            ?: $product->buy_price
        );

        $product->increment('stock', $quantity);

        $this->refreshInventory($product);

        $this->createMovement(

            product: $product,

            movementType: $movementType,

            referenceNo: $referenceNo,

            referenceId: $referenceId,

            qtyIn: $quantity,

            qtyOut: 0,

            unitCost: $unitCost,

            remark: $remark

        );
    }

    /**
     * Reduce inventory.
     *
     * Used by:
     * - Sale
     * - Purchase Cancel
     * - Stock Adjustment (-)
     */
    public function stockOut(
        Product $product,
        float $quantity,
        string $referenceNo,
        int $referenceId,
        ?float $unitCost = null,
        string $movementType = 'Sale',
        string $remark = 'Stock Out'
    ): void {

        $quantity = $this->normalize($quantity);

	$this->validateQuantity($quantity);

	$this->validateStock($product, $quantity);

        $unitCost ??= (float) (
            $product->average_cost
            ?: $product->buy_price
        );

        $product->decrement('stock', $quantity);

        $this->refreshInventory($product);

        $this->createMovement(

            product: $product,

            movementType: $movementType,

            referenceNo: $referenceNo,

            referenceId: $referenceId,

            qtyIn: 0,

            qtyOut: $quantity,

            unitCost: $unitCost,

            remark: $remark

        );
    }
    /**
     * Reverse stock received from a purchase.
     */
    public function purchaseReturn(
        Product $product,
        float $quantity,
        string $referenceNo,
        int $referenceId,
        ?float $unitCost = null,
        string $remark = 'Purchase Return'
    ): void {
        $this->stockOut(
            product: $product,
            quantity: $quantity,
            referenceNo: $referenceNo,
            referenceId: $referenceId,
            unitCost: $unitCost,
            movementType: 'Purchase Return',
            remark: $remark
        );
    }

    /**
     * Return stock from a cancelled or returned sale.
     */
    public function saleReturn(
        Product $product,
        float $quantity,
        string $referenceNo,
        int $referenceId,
        ?float $unitCost = null,
        string $remark = 'Sales Return'
    ): void {
        $this->stockIn(
            product: $product,
            quantity: $quantity,
            referenceNo: $referenceNo,
            referenceId: $referenceId,
            unitCost: $unitCost,
            movementType: 'Sales Return',
            remark: $remark
        );
    }

    /**
     * Perform a manual stock adjustment.
     *
     * Positive quantity increases stock.
     * Negative quantity decreases stock.
     */
    public function adjustStock(
        Product $product,
        float $quantity,
        string $referenceNo,
        int $referenceId,
        ?float $unitCost = null,
        string $remark = 'Stock Adjustment'
    ): void {

        if ($quantity == 0) {
            return;
        }

        if ($quantity > 0) {

            $this->stockIn(
                product: $product,
                quantity: $quantity,
                referenceNo: $referenceNo,
                referenceId: $referenceId,
                unitCost: $unitCost,
                movementType: 'Adjustment',
                remark: $remark
            );

            return;
        }

        $this->stockOut(
            product: $product,
            quantity: abs($quantity),
            referenceNo: $referenceNo,
            referenceId: $referenceId,
            unitCost: $unitCost,
            movementType: 'Adjustment',
            remark: $remark
        );
    }

    /**
     * Create a stock movement record.
     */
    protected function createMovement(
        Product $product,
        string $movementType,
        string $referenceNo,
        int $referenceId,
        float $qtyIn,
        float $qtyOut,
        float $unitCost,
        string $remark
    ): void {

        StockMovement::create([

            'product_id' => $product->id,

            'movement_type' => $movementType,

            'reference_no' => $referenceNo,

            'reference_id' => $referenceId,

            'qty_in' => $qtyIn,

            'qty_out' => $qtyOut,

            'balance' => $product->stock,

            'unit_cost' => $unitCost,

            'remark' => $remark,

            'created_by' => Auth::id(),

        ]);
    }
    /**
     * Normalize decimal values.
     */
    protected function normalize(
        float|int|string|null $value
    ): float {
        return round((float) ($value ?? 0), 2);
    }

    /**
     * Determine whether the product has sufficient stock.
     */
    public function hasAvailableStock(
        Product $product,
        float $requiredQuantity
    ): bool {
        return $this->normalize($product->stock)
            >= $this->normalize($requiredQuantity);
    }

    /**
     * Get the preferred inventory cost.
     *
     * Future compatible with:
     * - FIFO
     * - LIFO
     * - Batch Cost
     * - Warehouse Cost
     */
    public function getUnitCost(
        Product $product
    ): float {

        if (
            isset($product->average_cost)
            && $product->average_cost > 0
        ) {
            return $this->normalize(
                $product->average_cost
            );
        }

        if (
            property_exists($product, 'last_cost')
            && !empty($product->last_cost)
        ) {
            return $this->normalize(
                $product->last_cost
            );
        }

        return $this->normalize(
            $product->buy_price
        );
    }

    /**
     * Refresh product after inventory movement.
     *
     * Centralized hook for future enhancements:
     * - Recalculate average cost
     * - Warehouse balances
     * - Batch balances
     * - Inventory cache
     */
    protected function refreshInventory(
        Product $product
    ): Product {

        $product->refresh();

        return $product;
    }

    /**
     * Validate movement quantity.
     */
    protected function validateQuantity(
        float $quantity
    ): void {

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be greater than zero.',
            ]);
        }
    }

    /**
     * Validate available inventory.
     */
    protected function validateStock(
    Product $product,
    float $quantity
): void {

    // Always validate against the latest database state.
    $product = Product::query()->findOrFail($product->id);

    if (
        $this->normalize($product->stock)
        < $this->normalize($quantity)
    ) {

        throw ValidationException::withMessages([
            'stock' => sprintf(
                '%s has only %.2f remaining.',
                $product->product_name,
                $product->stock
            ),
        ]);

    }
}
    /**
     * Audit hook.
     *
     * Reserved for future:
     * - Activity Log
     * - Audit Trail
     * - Event Dispatching
     * - Notifications
     */
    protected function audit(
        string $action,
        Product $product,
        array $context = []
    ): void {
        // Future implementation.
    }

    /**
     * Event hook.
     *
     * Reserved for future domain events.
     */
    protected function dispatchInventoryEvent(
        string $event,
        Product $product,
        array $payload = []
    ): void {
        // Future implementation.
    }
}
