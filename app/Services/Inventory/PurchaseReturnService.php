<?php

namespace App\Services\Inventory;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Services\Shared\NumberGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PurchaseReturnService
{
    public function __construct(
        protected StockService $stockService,
        protected CostService $costService
    ) {}

    public function generateReturnNumber(): string
    {
        return NumberGeneratorService::generate(
            'PR',
            PurchaseReturn::class,
            'return_no'
        );
    }

    /**
     * Create a partial purchase return.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(Purchase $purchase, array $data): PurchaseReturn
    {
        return DB::transaction(function () use ($purchase, $data) {
            $purchase->load('details.product', 'returns.details');

            if (! $purchase->isCompleted()) {
                throw new InvalidArgumentException(
                    'Only Completed purchases can be returned.'
                );
            }

            $items = $this->returnItems($purchase, $data);

            if (count($items) === 0) {
                throw new InvalidArgumentException(
                    'Please enter at least one return quantity.'
                );
            }

            $subtotal = collect($items)->sum('subtotal');

            $purchaseReturn = PurchaseReturn::create([
                'return_no' => $this->generateReturnNumber(),
                'return_date' => $data['return_date'],
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'subtotal' => round($subtotal, 2),
                'status' => 'Completed',
                'remark' => $data['remark'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($items as $item) {
                $detail = $item['detail'];
                $product = $detail->product;

                PurchaseReturnDetail::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'purchase_detail_id' => $detail->id,
                    'product_id' => $detail->product_id,
                    'qty' => $item['qty'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['subtotal'],
                    'reason' => $item['reason'],
                ]);

                $this->stockService->purchaseReturn(
                    $product,
                    $item['qty'],
                    $item['unit_cost'],
                    $purchaseReturn->return_no,
                    $purchaseReturn->id,
                    'Purchase Return'
                );

                $this->costService->recalculateAverageCost($product);
            }

            return $purchaseReturn->fresh([
                'purchase',
                'supplier',
                'details.product',
                'details.purchaseDetail',
                'creator',
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, array<string, mixed>>
     */
    private function returnItems(Purchase $purchase, array $data): array
    {
        $items = [];

        foreach ($data['purchase_detail_id'] as $index => $purchaseDetailId) {
            $qty = (float) ($data['qty'][$index] ?? 0);

            if ($qty <= 0) {
                continue;
            }

            $detail = PurchaseDetail::with('product', 'returnDetails.purchaseReturn')
                ->findOrFail($purchaseDetailId);

            if ((int) $detail->purchase_id !== (int) $purchase->id) {
                throw new InvalidArgumentException(
                    'Return item does not belong to this purchase.'
                );
            }

            if (! $detail->product) {
                throw new InvalidArgumentException(
                    'Cannot return purchase because one product no longer exists.'
                );
            }

            $returnableQty = $detail->returnableQty();

            if ($qty > $returnableQty) {
                throw new InvalidArgumentException(
                    'Return quantity cannot exceed the remaining purchased quantity.'
                );
            }

            $detail->product->refresh();

            if ((float) $detail->product->stock < $qty) {
                throw new InvalidArgumentException(
                    'Cannot return purchase because available stock is lower than the return quantity.'
                );
            }

            $unitCost = (float) $detail->unit_cost;

            $items[] = [
                'detail' => $detail,
                'qty' => $qty,
                'unit_cost' => $unitCost,
                'subtotal' => round($qty * $unitCost, 2),
                'reason' => $data['reason'][$index] ?? null,
            ];
        }

        return $items;
    }
}
