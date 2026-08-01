<?php

namespace App\Services\Inventory;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        protected StockService $stockService,
        protected CostService $costService
    ) {
    }

    /**
     * Receive inventory from a completed purchase.
     */
    public function receivePurchase(
        Product $product,
        float $quantity,
        float $unitCost,
        string $referenceNo,
        int $referenceId,
        string $remark = 'Purchase Stock In'
    ): void {
        DB::transaction(function () use (
            $product,
            $quantity,
            $unitCost,
            $referenceNo,
            $referenceId,
            $remark
        ) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->id);

            $this->costService->updatePurchaseCost(
                $product,
                $quantity,
                $unitCost
            );

            $this->stockService->stockIn(
                product: $product,
                quantity: $quantity,
                referenceNo: $referenceNo,
                referenceId: $referenceId,
                unitCost: $unitCost,
                movementType: 'Purchase',
                remark: $remark
            );
        });
    }

    /**
     * Issue inventory for a completed sale.
     */
    public function issueSale(
        Product $product,
        float $quantity,
        float $unitCost,
        string $referenceNo,
        int $referenceId,
        string $remark = 'Sales Stock Out'
    ): void {
        DB::transaction(function () use (
            $product,
            $quantity,
            $unitCost,
            $referenceNo,
            $referenceId,
            $remark
        ) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->id);

            $this->stockService->stockOut(
                product: $product,
                quantity: $quantity,
                referenceNo: $referenceNo,
                referenceId: $referenceId,
                unitCost: $unitCost,
                movementType: 'Sale',
                remark: $remark
            );
        });
    }

    /**
     * Reverse a completed purchase.
     */
    public function reversePurchase(
        Product $product,
        float $quantity,
        float $unitCost,
        string $referenceNo,
        int $referenceId,
        string $remark = 'Purchase Cancelled'
    ): void {
        DB::transaction(function () use (
            $product,
            $quantity,
            $unitCost,
            $referenceNo,
            $referenceId,
            $remark
        ) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->id);

            $this->stockService->purchaseReturn(
                product: $product,
                quantity: $quantity,
                referenceNo: $referenceNo,
                referenceId: $referenceId,
                unitCost: $unitCost,
                remark: $remark
            );

            $this->costService->recalculateAverageCost($product);
        });
    }

    /**
     * Reverse a completed sale.
     */
    public function reverseSale(
        Product $product,
        float $quantity,
        float $unitCost,
        string $referenceNo,
        int $referenceId,
        string $remark = 'Sales Return'
    ): void {
        DB::transaction(function () use (
            $product,
            $quantity,
            $unitCost,
            $referenceNo,
            $referenceId,
            $remark
        ) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->id);

            $this->stockService->saleReturn(
                product: $product,
                quantity: $quantity,
                referenceNo: $referenceNo,
                referenceId: $referenceId,
                unitCost: $unitCost,
                remark: $remark
            );
        });
    }

    /**
     * Perform a manual stock adjustment.
     */
    public function adjustStock(
        Product $product,
        float $quantity,
        ?float $unitCost,
        string $referenceNo,
        int $referenceId,
        string $remark = 'Stock Adjustment'
    ): void {
        DB::transaction(function () use (
            $product,
            $quantity,
            $unitCost,
            $referenceNo,
            $referenceId,
            $remark
        ) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($product->id);

            $this->stockService->adjustStock(
                product: $product,
                quantity: $quantity,
                referenceNo: $referenceNo,
                referenceId: $referenceId,
                unitCost: $unitCost,
                remark: $remark
            );
        });
    }
}