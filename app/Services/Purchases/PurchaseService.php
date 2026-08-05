<?php

namespace App\Services\Purchases;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\StockMovement;
use App\Services\Inventory\CostService;
use App\Services\Inventory\StockService;
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
     * Generate Purchase Number.
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
     * Create Purchase.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {

            $this->validatePurchaseData($data);

            $purchase = Purchase::create(array_merge(
                [
                    'purchase_no' => $this->generatePurchaseNumber(),
                    'created_by'  => Auth::id(),
                ],
                $this->purchaseAttributes($data)
            ));

            $this->saveDetails($purchase, $data);

            $purchase->load([
                'supplier',
                'details.product',
                'creator',
            ]);

            $this->afterPurchaseCreated($purchase);

            return $purchase;
        });
    }

    /**
     * Update Purchase.
     *
     * Only Draft or Pending purchases are editable.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        Purchase $purchase,
        array $data
    ): Purchase {

        return DB::transaction(function () use ($purchase, $data) {

            $purchase->load('details.product');

            if (! $purchase->isDraft() && ! $purchase->isPending()) {
                throw new InvalidArgumentException(
                    'Only Draft or Pending purchases can be updated.'
                );
            }

            $this->validatePurchaseData($data);

            $this->removePurchaseStockMovements($purchase);

            $purchase->details()->delete();

            $purchase->update(
                $this->purchaseAttributes($data)
            );

            $purchase->refresh();

            $this->saveDetails($purchase, $data);

            $purchase->load([
                'supplier',
                'details.product',
                'creator',
            ]);

            $this->afterPurchaseUpdated($purchase);

            return $purchase;
        });
    }

    /**
     * Cancel Purchase.
     */
    public function cancel(
        Purchase $purchase
    ): Purchase {

        return DB::transaction(function () use ($purchase) {

            $purchase->load('details.product');

            if ($purchase->isCancelled()) {
                throw new InvalidArgumentException(
                    "Purchase {$purchase->purchase_no} is already cancelled."
                );
            }

            if ($purchase->isCompleted()) {

                $this->ensurePurchaseCanBeReversed(
                    $purchase
                );

                foreach ($purchase->details as $detail) {

                    $this->reversePurchaseDetail(
                        $purchase,
                        $detail
                    );

                }

            }

            $purchase->update([
                'status' => 'Cancelled',
            ]);

            $purchase->refresh();

            $this->afterPurchaseCancelled($purchase);

            return $purchase->load([
                'supplier',
                'details.product',
                'creator',
            ]);
        });
    }

    /**
     * Delete Draft Purchase.
     */
    public function deleteDraft(
        Purchase $purchase
    ): void {

        DB::transaction(function () use ($purchase) {

            if (! $purchase->isDraft()) {

                throw new InvalidArgumentException(
                    'Only Draft purchases can be deleted.'
                );

            }

            $purchase->load('details.product');

            $this->removePurchaseStockMovements(
                $purchase
            );

            $purchase->details()->delete();

            $purchase->delete();

            $this->afterPurchaseDeleted(
                $purchase
            );

        });

    }
    /**
     * Validate purchase payload.
     *
     * @param array<string,mixed> $data
     */
    private function validatePurchaseData(array $data): void
    {
        $this->ensureHasProducts($data);

        $required = [
            'product_id',
            'qty',
            'unit_cost',
            'subtotal_item',
        ];

        foreach ($required as $field) {
            if (! isset($data[$field]) || ! is_array($data[$field])) {
                throw new InvalidArgumentException(
                    "Missing purchase field: {$field}."
                );
            }
        }

        $count = count($data['product_id']);

        foreach (['qty', 'unit_cost', 'subtotal_item'] as $field) {
            if (count($data[$field]) !== $count) {
                throw new InvalidArgumentException(
                    "Purchase item data is inconsistent."
                );
            }
        }
    }

    /**
     * Ensure purchase contains at least one product.
     *
     * @param array<string,mixed> $data
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
     * Purchase master attributes.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function purchaseAttributes(array $data): array
    {
        return [
            'purchase_date'     => $data['purchase_date'],
            'supplier_id'       => $data['supplier_id'],
            'invoice_no'        => $data['invoice_no'] ?? null,

            'subtotal'          => $data['subtotal'] ?? 0,

            'discount_percent'  => $data['discount_percent'] ?? 0,
            'discount_amount'   => $data['discount_amount'] ?? 0,

            'tax_percent'       => $data['tax_percent'] ?? 0,
            'tax_amount'        => $data['tax_amount'] ?? 0,

            'grand_total'       => $data['grand_total'] ?? 0,

            'paid_amount'       => $data['paid_amount'] ?? 0,
            'balance'           => $data['balance'] ?? 0,

            'remark'            => $data['remark'] ?? null,

            'status'            => $data['status'] ?? 'Draft',
        ];
    }

    /**
     * Save purchase details.
     *
     * @param array<string,mixed> $data
     */
    private function saveDetails(
        Purchase $purchase,
        array $data
    ): void {

        $products = Product::query()
            ->whereIn('id', $data['product_id'])
            ->get()
            ->keyBy('id');

        foreach ($data['product_id'] as $index => $productId) {

            /** @var Product|null $product */
            $product = $products->get($productId);

            if (! $product) {
                throw new InvalidArgumentException(
                    "Product {$productId} does not exist."
                );
            }

            $qty = (float) $data['qty'][$index];
            $unitCost = (float) $data['unit_cost'][$index];

            if ($qty <= 0) {
                throw new InvalidArgumentException(
                    "{$product->product_name}: Quantity must be greater than zero."
                );
            }

            if ($unitCost < 0) {
                throw new InvalidArgumentException(
                    "{$product->product_name}: Unit cost cannot be negative."
                );
            }

            $detail = PurchaseDetail::create([

                'purchase_id' => $purchase->id,

                'product_id'  => $product->id,

                'qty'         => $qty,

                'unit_cost'   => $unitCost,

                'discount_percent'
                    => $data['discount_percent_item'][$index] ?? 0,

                'discount_amount'
                    => $data['discount_amount_item'][$index] ?? 0,

                'subtotal'
                    => $data['subtotal_item'][$index],

                'remark' => null,

            ]);

            if ($purchase->isCompleted()) {

                $this->receivePurchaseDetail(
                    $purchase,
                    $product,
                    $detail
                );

            }

        }

    }

    /**
     * Receive inventory from purchase.
     */
    private function receivePurchaseDetail(
        Purchase $purchase,
        Product $product,
        PurchaseDetail $detail
    ): void {

        $qty = (float) $detail->qty;
        $unitCost = (float) $detail->unit_cost;

        $this->costService->updatePurchaseCost(
            $product,
            $qty,
            $unitCost
        );

        $this->stockService->stockIn(
            product: $product,
            quantity: $qty,
            referenceNo: $purchase->purchase_no,
            referenceId: $purchase->id,
            unitCost: $unitCost,
            movementType: 'Purchase',
            remark: 'Purchase Stock In'
        );
    }

    /**
     * Reverse one purchase detail.
     */
    private function reversePurchaseDetail(
        Purchase $purchase,
        PurchaseDetail $detail
    ): void {

        $product = $detail->product;

        if (! $product) {
            throw new InvalidArgumentException(
                'Purchase contains an invalid product.'
            );
        }

        $qty = (float) $detail->qty;
        $unitCost = (float) $detail->unit_cost;

        $this->stockService->purchaseReturn(
            product: $product,
            quantity: $qty,
            referenceNo: $purchase->purchase_no,
            referenceId: $purchase->id,
            unitCost: $unitCost,
            remark: 'Purchase Cancelled'
        );

        $this->costService->recalculateAverageCost(
            $product
        );
    }
    /**
     * Ensure purchase can be reversed.
     */
    private function ensurePurchaseCanBeReversed(
        Purchase $purchase
    ): void {

        foreach ($purchase->details as $detail) {

            $product = $detail->product;

            if (! $product) {
                throw new InvalidArgumentException(
                    'Cannot reverse purchase because one or more products no longer exist.'
                );
            }

            $product->refresh();

            if ((float) $product->stock < (float) $detail->qty) {

                throw new InvalidArgumentException(sprintf(
                    'Cannot cancel Purchase %s because %s only has %.2f remaining.',
                    $purchase->purchase_no,
                    $product->product_name,
                    $product->stock
                ));

            }

        }

    }

    /**
     * Remove legacy purchase stock movements.
     */
    private function removePurchaseStockMovements(
        Purchase $purchase
    ): void {

        StockMovement::query()
            ->where('movement_type', 'Purchase')
            ->where('reference_no', $purchase->purchase_no)
            ->where('reference_id', $purchase->id)
            ->get()
            ->each(function (StockMovement $movement): void {

                $product = $movement->product;

                if ($product) {

                    $product->decrement(
                        'stock',
                        (float) $movement->qty_in
                    );

                    $product->refresh();

                    $this->costService->recalculateAverageCost(
                        $product
                    );

                }

                $movement->delete();

            });

    }

    /**
     * Purchase created hook.
     *
     * Reserved for:
     * - Audit Log
     * - Notifications
     * - Event Dispatching
     */
    protected function afterPurchaseCreated(
        Purchase $purchase
    ): void {
        //
    }

    /**
     * Purchase updated hook.
     */
    protected function afterPurchaseUpdated(
        Purchase $purchase
    ): void {
        //
    }

    /**
     * Purchase cancelled hook.
     */
    protected function afterPurchaseCancelled(
        Purchase $purchase
    ): void {
        //
    }

    /**
     * Purchase deleted hook.
     */
    protected function afterPurchaseDeleted(
        Purchase $purchase
    ): void {
        //
    }
}
