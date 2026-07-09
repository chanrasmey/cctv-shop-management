<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\StockMovement;
use App\Services\Shared\NumberGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseService
{
    protected StockService $stockService;

    protected CostService $costService;

    public function __construct(
        StockService $stockService,
        CostService $costService
    ) {
        $this->stockService = $stockService;
        $this->costService = $costService;
    }

    /**
     * Generate Purchase Number
     */
    public function generatePurchaseNumber(): string
    {
        return NumberGeneratorService::generate(
            'PO',
            Purchase::class,
            'purchase_no'
        );
    }

    /**
     * Save purchase.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $this->ensureHasProducts($data);

            $purchase = Purchase::create(array_merge(
                [
                    'purchase_no' => $this->generatePurchaseNumber(),
                    'created_by' => Auth::id(),
                ],
                $this->purchaseAttributes($data)
            ));

            $this->saveDetails($purchase, $data);

            return $purchase;
        });
    }

    /**
     * Update a purchase that has not yet been received into inventory.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            $purchase->load('details.product');

            if (! $purchase->isDraft() && ! $purchase->isPending()) {
                throw new InvalidArgumentException(
                    'Only Draft or Pending purchases can be updated.'
                );
            }

            $this->ensureHasProducts($data);
            $this->removePurchaseStockMovements($purchase);

            $purchase->details()->delete();
            $purchase->update($this->purchaseAttributes($data));
            $purchase->refresh();

            $this->saveDetails($purchase, $data);

            return $purchase->fresh(['supplier', 'details.product', 'creator']);
        });
    }

    /**
     * Cancel a purchase and reverse completed stock receipts.
     */
    public function cancel(Purchase $purchase): Purchase
    {
        return DB::transaction(function () use ($purchase) {
            $purchase->load('details.product');

            if ($purchase->isCancelled()) {
                throw new InvalidArgumentException(
                    'Purchase is already cancelled.'
                );
            }

            if ($purchase->isCompleted()) {
                $this->ensurePurchaseCanBeReversed($purchase);

                foreach ($purchase->details as $detail) {
                    $this->reversePurchaseDetail($purchase, $detail);
                }
            }

            $purchase->update([
                'status' => 'Cancelled',
            ]);

            return $purchase->fresh(['supplier', 'details.product', 'creator']);
        });
    }

    /**
     * Delete a draft purchase.
     */
    public function deleteDraft(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            $purchase->load('details.product');

            if (! $purchase->isDraft()) {
                throw new InvalidArgumentException(
                    'Only Draft purchases can be deleted.'
                );
            }

            $this->removePurchaseStockMovements($purchase);

            $purchase->details()->delete();
            $purchase->delete();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function ensureHasProducts(array $data): void
    {
        if (
            ! isset($data['product_id']) ||
            count($data['product_id']) === 0
        ) {
            throw new InvalidArgumentException(
                'Please add at least one product.'
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function purchaseAttributes(array $data): array
    {
        return [
            'purchase_date' => $data['purchase_date'],
            'supplier_id' => $data['supplier_id'],
            'invoice_no' => $data['invoice_no'] ?? null,
            'subtotal' => $data['subtotal'] ?? 0,
            'discount_percent' => $data['discount_percent'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'tax_percent' => $data['tax_percent'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
            'paid_amount' => $data['paid_amount'] ?? 0,
            'balance' => $data['balance'] ?? 0,
            'remark' => $data['remark'] ?? null,
            'status' => $data['status'] ?? 'Draft',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function saveDetails(Purchase $purchase, array $data): void
    {
        foreach ($data['product_id'] as $index => $productId) {
            $product = Product::findOrFail($productId);

            $detail = PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => $productId,
                'qty' => $data['qty'][$index],
                'unit_cost' => $data['unit_cost'][$index],
                'discount_percent' =>
                    $data['discount_percent_item'][$index] ?? 0,
                'discount_amount' =>
                    $data['discount_amount_item'][$index] ?? 0,
                'subtotal' =>
                    $data['subtotal_item'][$index],
                'remark' => null,
            ]);

            if ($purchase->isCompleted()) {
                $this->receivePurchaseDetail($purchase, $product, $detail);
            }
        }
    }

    /**
     * Receive one purchase detail into inventory.
     */
    private function receivePurchaseDetail(
        Purchase $purchase,
        Product $product,
        PurchaseDetail $detail
    ): void {
        $this->costService->updatePurchaseCost(
            $product,
            (float) $detail->qty,
            (float) $detail->unit_cost
        );

        $this->stockService->stockIn(
            $product,
            (float) $detail->qty,
            (float) $detail->unit_cost,
            $purchase->purchase_no,
            $purchase->id
        );
    }

    /**
     * Make sure every received product can be reversed without negative stock.
     */
    private function ensurePurchaseCanBeReversed(Purchase $purchase): void
    {
        foreach ($purchase->details as $detail) {
            $product = $detail->product;

            if (! $product) {
                throw new InvalidArgumentException(
                    'Cannot cancel purchase because one product no longer exists.'
                );
            }

            $product->refresh();

            if ((float) $product->stock < (float) $detail->qty) {
                throw new InvalidArgumentException(
                    'Cannot cancel purchase because available stock is lower than the purchased quantity.'
                );
            }
        }
    }

    /**
     * Reverse one completed purchase detail from inventory.
     */
    private function reversePurchaseDetail(Purchase $purchase, PurchaseDetail $detail): void
    {
        $product = $detail->product;

        if (! $product) {
            throw new InvalidArgumentException(
                'Cannot cancel purchase because one product no longer exists.'
            );
        }

        $this->stockService->purchaseReturn(
            $product,
            (float) $detail->qty,
            (float) $detail->unit_cost,
            $purchase->purchase_no,
            $purchase->id,
            'Purchase Cancelled'
        );

        $this->costService->recalculateAverageCost($product);
    }

    /**
     * Remove purchase stock movements if a legacy draft already affected stock.
     */
    private function removePurchaseStockMovements(Purchase $purchase): void
    {
        StockMovement::query()
            ->where('movement_type', 'Purchase')
            ->where('reference_no', $purchase->purchase_no)
            ->where('reference_id', $purchase->id)
            ->get()
            ->each(function (StockMovement $movement): void {
                $product = $movement->product;

                if ($product) {
                    $product->decrement('stock', (float) $movement->qty_in);
                    $product->refresh();

                    $this->costService->recalculateAverageCost($product);
                }

                $movement->delete();
            });
    }
}
