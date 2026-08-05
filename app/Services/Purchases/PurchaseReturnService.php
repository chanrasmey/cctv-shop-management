<?php

namespace App\Services\Purchases;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Services\Inventory\CostService;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseReturnService
{
    public function __construct(
        protected StockService $stockService,
        protected CostService $costService
    ) {
    }

    /**
     * Create Purchase Return.
     */
    public function create(
        Purchase $purchase,
        array $data
    ): PurchaseReturn {

        return DB::transaction(function () use (
            $purchase,
            $data
        ) {

            /**
             * Always use a fresh Purchase instance.
             */
            $purchase = Purchase::query()
                ->with([
                    'supplier',
                    'details',
                    'details.product',
                ])
                ->findOrFail($purchase->id);

            /**
             * Business Rule:
             * Only completed purchases may be returned.
             */
            if ($purchase->status !== 'Completed') {
                throw new InvalidArgumentException(
                    'Only Completed purchases can be returned.'
                );
            }

            /**
             * Validate request.
             */
            $this->validateReturnData(
                $purchase,
                $data
            );

            /**
             * Create Purchase Return header.
             */
            $purchaseReturn = PurchaseReturn::create([
                'return_no'   => $this->generateReturnNumber(),
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'return_date' => $data['return_date']
                    ?? now()->toDateString(),
                'subtotal'    => 0,
                'remark'      => $data['remark'] ?? null,
                'status'      => 'Completed',
                'created_by'  => Auth::id(),
            ]);

            /**
             * Process return items.
             */
            $subtotal = $this->processDetails(
                $purchase,
                $purchaseReturn,
                $data
            );

            /**
             * Update totals.
             */
            $purchaseReturn->update([
                'subtotal' => $subtotal,
            ]);

            /**
             * Optional model helper.
             */
            if (
                method_exists(
                    $purchaseReturn,
                    'recalculateTotals'
                )
            ) {
                $purchaseReturn->recalculateTotals();
            }

            /**
             * Reload relationships.
             */
            $purchaseReturn->load([
                'purchase',
                'supplier',
                'details.product',
            ]);

            /**
             * Hook.
             */
            $this->afterReturnCreated(
                $purchaseReturn
            );

            return $purchaseReturn;
        });
    }

    /**
     * Generate Purchase Return Number.
     */
    public function generateReturnNumber(): string
    {
        $today = now()->format('Ymd');

        $last = PurchaseReturn::query()
            ->whereDate(
                'created_at',
                today()
            )
            ->latest('id')
            ->first();

        $running = 1;

        if ($last) {

            $parts = explode(
                '-',
                $last->return_no
            );

            $running = ((int) end($parts)) + 1;
        }

        return sprintf(
            'PRN-%s-%04d',
            $today,
            $running
        );
    }
    /**
     * Validate Purchase Return request.
     */
    protected function validateReturnData(
        Purchase $purchase,
        array $data
    ): void {

        if (
            ! isset($data['purchase_detail_id']) ||
            ! is_array($data['purchase_detail_id']) ||
            count($data['purchase_detail_id']) === 0
        ) {
            throw new InvalidArgumentException(
                'Purchase return details are required.'
            );
        }

        if (
            ! isset($data['qty']) ||
            ! is_array($data['qty'])
        ) {
            throw new InvalidArgumentException(
                'Purchase return quantities are required.'
            );
        }

        if (
            count($data['purchase_detail_id']) !==
            count($data['qty'])
        ) {
            throw new InvalidArgumentException(
                'Purchase return details are invalid.'
            );
        }

        foreach ($data['purchase_detail_id'] as $index => $detailId) {

            $purchaseDetail = PurchaseDetail::query()
                ->find($detailId);

            if (! $purchaseDetail) {
                throw new InvalidArgumentException(
                    'Return item does not belong to this purchase.'
                );
            }

            if (
                (int) $purchaseDetail->purchase_id !==
                (int) $purchase->id
            ) {
                throw new InvalidArgumentException(
                    'Return item does not belong to this purchase.'
                );
            }

            $qty = (float) (
                $data['qty'][$index] ?? 0
            );

            if ($qty <= 0) {
                throw new InvalidArgumentException(
                    'Return quantity must be greater than zero.'
                );
            }

            $remaining = $this->remainingQuantity(
                $purchaseDetail
            );

            if ($qty > $remaining) {
                throw new InvalidArgumentException(
                    'Return quantity cannot exceed the remaining purchased quantity.'
                );
            }
        }
    }
    /**
     * Process Purchase Return Details.
     */
    protected function processDetails(
        Purchase $purchase,
        PurchaseReturn $purchaseReturn,
        array $data
    ): float {

        $subtotal = 0;

        foreach ($data['purchase_detail_id'] as $index => $detailId) {

            /**
             * Always reload Purchase Detail directly from database.
             */
            $purchaseDetail = PurchaseDetail::query()
                ->findOrFail($detailId);

            if (
                (int) $purchaseDetail->purchase_id !==
                (int) $purchase->id
            ) {
                throw new InvalidArgumentException(
                    'Return item does not belong to this purchase.'
                );
            }

            /**
             * Always reload Product directly from database.
             */
            $product = Product::query()->find(
                $purchaseDetail->product_id
            );

            if (! $product) {
                throw new InvalidArgumentException(
                    'Product no longer exists.'
                );
            }

            /**
             * Return quantity.
             */
            $qty = (float) (
                $data['qty'][$index] ?? 0
            );

            /**
             * Remaining quantity validation.
             */
            $remaining = $this->remainingQuantity(
                $purchaseDetail
            );

            if ($qty > $remaining) {
                throw new InvalidArgumentException(
                    'Return quantity cannot exceed the remaining purchased quantity.'
                );
            }

            /**
             * IMPORTANT
             * Always check CURRENT stock from database.
             */
            $currentStock = (float) Product::query()
    ->whereKey($product->id)
    ->value('stock');


if ($currentStock < $qty) {
    throw new InvalidArgumentException(
        'Cannot return purchase because available stock is lower than the return quantity.'
    );
}

            /**
             * Determine unit cost.
             */
            $unitCost = (float) (
                $purchaseDetail->unit_cost
                ?? $purchaseDetail->cost
                ?? $product->average_cost
                ?? 0
            );

            /**
             * Line subtotal.
             */
            $lineSubtotal = round(
                $qty * $unitCost,
                2
            );

            /**
             * Create return detail.
             */
            PurchaseReturnDetail::create([
                'purchase_return_id' => $purchaseReturn->id,
                'purchase_detail_id' => $purchaseDetail->id,
                'product_id'         => $product->id,
                'qty'                => $qty,
                'unit_cost'          => $unitCost,
                'subtotal'           => $lineSubtotal,
            ]);

            /**
             * Reduce inventory.
             */
            $this->stockService->purchaseReturn(
                $product,
                $qty,
                $purchaseReturn->return_no,
                $purchaseReturn->id
            );

            /**
             * Refresh average cost.
             */
            $this->costService->recalculateAverageCost(
                $product
            );

            $subtotal += $lineSubtotal;
        }

        return round(
            $subtotal,
            2
        );
    }
    /**
     * Cancel Purchase Return.
     */
    public function cancel(
        PurchaseReturn $purchaseReturn
    ): PurchaseReturn {

        return DB::transaction(function () use ($purchaseReturn) {

            $purchaseReturn->loadMissing([
                'details.product',
            ]);

            if ($purchaseReturn->status === 'Cancelled') {
                throw new InvalidArgumentException(
                    'Purchase Return is already cancelled.'
                );
            }

            foreach ($purchaseReturn->details as $detail) {

                if (! $detail->product) {
                    continue;
                }

                /**
                 * Restore stock.
                 */
                $this->stockService->stockIn(
                    product: $detail->product,
                    quantity: (float) $detail->qty,
                    referenceNo: $purchaseReturn->return_no,
                    referenceId: $purchaseReturn->id,
                    unitCost: (float) $detail->unit_cost,
                    movementType: 'Purchase Return',
                    remark: 'Purchase Return Cancelled'
                );

                /**
                 * Refresh product average cost.
                 */
                $this->refreshProductCost(
                    $detail->product
                );
            }

            $purchaseReturn->update([
                'status' => 'Cancelled',
            ]);

            $this->afterReturnCancelled(
                $purchaseReturn
            );

            return $purchaseReturn->fresh([
                'purchase',
                'supplier',
                'details.product',
            ]);
        });
    }
    /**
     * Remaining quantity that can still be returned.
     */
    protected function remainingQuantity(
        PurchaseDetail $purchaseDetail
    ): float {

        return max(
            0,
            (float) $purchaseDetail->qty -
            $this->returnedQuantity($purchaseDetail)
        );
    }

    /**
     * Total quantity already returned.
     */
    protected function returnedQuantity(
        PurchaseDetail $purchaseDetail
    ): float {

        return (float) PurchaseReturnDetail::query()
            ->where('purchase_detail_id', $purchaseDetail->id)
            ->whereHas('purchaseReturn', function ($query) {
                $query->where('status', 'Completed');
            })
            ->sum('qty');
    }

    /**
     * Determine whether this Purchase Detail
     * can still be returned.
     */
    protected function canReturn(
        PurchaseDetail $purchaseDetail,
        float $qty
    ): bool {

        return $qty <= $this->remainingQuantity(
            $purchaseDetail
        );
    }

    /**
     * Refresh Product Average Cost.
     */
    protected function refreshProductCost(
        Product $product
    ): void {

        $this->costService->recalculateAverageCost(
            $product->fresh()
        );
    }
    /**
     * Hook executed after a Purchase Return is created.
     */
    protected function afterReturnCreated(
        PurchaseReturn $purchaseReturn
    ): void {

        $this->audit(
            'purchase_return.created',
            $purchaseReturn
        );

        $this->dispatchEvent(
            'purchase-return.created',
            $purchaseReturn
        );
    }

    /**
     * Hook executed after a Purchase Return is cancelled.
     */
    protected function afterReturnCancelled(
        PurchaseReturn $purchaseReturn
    ): void {

        $this->audit(
            'purchase_return.cancelled',
            $purchaseReturn
        );

        $this->dispatchEvent(
            'purchase-return.cancelled',
            $purchaseReturn
        );
    }

    /**
     * Audit placeholder.
     * Extend this method when an audit log
     * module is implemented.
     */
    protected function audit(
        string $action,
        PurchaseReturn $purchaseReturn
    ): void {
        // Reserved for Audit Trail.
    }

    /**
     * Event placeholder.
     * Extend this method when ERP events
     * are implemented.
     */
    protected function dispatchEvent(
        string $event,
        PurchaseReturn $purchaseReturn
    ): void {
        // Reserved for future events.
    }
}
