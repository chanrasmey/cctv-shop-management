<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SalesReturnService
{
    protected StockService $stockService;

    public function __construct(
        StockService $stockService
    ) {
        $this->stockService = $stockService;
    }

    /**
     * Generate next Sales Return number.
     */
    public function generateReturnNumber(): string
    {
        $date = now()->format('Ymd');

        $latest = SalesReturn::query()
            ->whereDate('created_at', today())
            ->latest('id')
            ->first();

        $running = 1;

        if ($latest) {
            $parts = explode('-', $latest->return_no);
            $running = ((int) end($parts)) + 1;
        }

        return sprintf(
            'SRN-%s-%04d',
            $date,
            $running
        );
    }

    /**
     * Create Sales Return.
     */
    public function create(
        Sale $sale,
        array $data
    ): SalesReturn {

        return DB::transaction(function () use (
            $sale,
            $data
        ) {

            $this->validateSale($sale);

            $salesReturn = SalesReturn::create([
                'return_no'   => $this->generateReturnNumber(),
                'return_date' => $data['return_date'],
                'sale_id'     => $sale->id,
                'customer_id' => $sale->customer_id,
                'subtotal'    => 0,
                'remark'      => $data['remark'] ?? null,
                'status'      => 'Completed',
                'created_by'  => Auth::id(),
            ]);

            foreach ($data['sale_detail_id'] as $index => $detailId) {

                $detail = SaleDetail::query()->findOrFail($detailId);

                $qty = (float) $data['qty'][$index];

                $this->validateSaleDetail(
                    $sale,
                    $detail
                );

                $this->validateRemainingQty(
                    $detail,
                    $qty
                );

                $this->createReturnDetail(
                    salesReturn: $salesReturn,
                    detail: $detail,
                    qty: $qty,
                    reason: $data['reason'][$index] ?? null,
                    remark: $data['remark_item'][$index] ?? null
                );
            }

            $salesReturn->recalculateTotals();

            $this->recalculateSale($sale);

            return $salesReturn->fresh([
                'details.product',
            ]);
        });
    }

    /**
     * Validate Sale status.
     */
    protected function validateSale(
        Sale $sale
    ): void {

        if ($sale->isDraft()) {
            throw new InvalidArgumentException(
                'Only Completed sales can be returned.'
            );
        }

        if ($sale->isPending()) {
            throw new InvalidArgumentException(
                'Only Completed sales can be returned.'
            );
        }

        if ($sale->isCancelled()) {
            throw new InvalidArgumentException(
                'Cancelled sales cannot be returned.'
            );
        }

        if (! $sale->isCompleted()) {
            throw new InvalidArgumentException(
                'Only Completed sales can be returned.'
            );
        }
    }

    /**
     * Ensure the Sale Detail belongs to the Sale.
     */
    protected function validateSaleDetail(
        Sale $sale,
        SaleDetail $detail
    ): void {

        if ($detail->sale_id !== $sale->id) {
            throw new InvalidArgumentException(
                'Return item does not belong to this Sale.'
            );
        }
    }

    /**
     * Validate remaining return quantity.
     */
    protected function validateRemainingQty(
        SaleDetail $detail,
        float $qty
    ): void {

        $returned = SalesReturnDetail::query()
            ->where(
                'sale_detail_id',
                $detail->id
            )
            ->sum('qty');

        $remaining = (float) $detail->qty - (float) $returned;

        if ($qty > $remaining) {
            throw new InvalidArgumentException(
                'Return quantity cannot exceed the remaining sold quantity.'
            );
        }

        if ($qty <= 0) {
            throw new InvalidArgumentException(
                'Return quantity must be greater than zero.'
            );
        }
    }
    /**
     * Create Sales Return Detail.
     */
    protected function createReturnDetail(
        SalesReturn $salesReturn,
        SaleDetail $detail,
        float $qty,
        ?string $reason = null,
        ?string $remark = null
    ): SalesReturnDetail {

        $subtotal = round(
            $qty * (float) $detail->unit_price,
            2
        );

        $profitAdjustment = round(
            (
                (float) $detail->unit_price -
                (float) $detail->unit_cost
            ) * $qty,
            2
        );

        $returnDetail = SalesReturnDetail::create([
            'sales_return_id'   => $salesReturn->id,
            'sale_detail_id'    => $detail->id,
            'product_id'        => $detail->product_id,
            'qty'               => $qty,
            'unit_price'        => $detail->unit_price,
            'unit_cost'         => $detail->unit_cost,
            'subtotal'          => $subtotal,
            'profit_adjustment' => $profitAdjustment,
            'reason'            => $reason,
            'remark'            => $remark,
        ]);

        $this->restoreStock(
            product: $detail->product,
            salesReturn: $salesReturn,
            detail: $returnDetail
        );

        return $returnDetail;
    }

    /**
     * Restore inventory after Sales Return.
     */
    protected function restoreStock(
        Product $product,
        SalesReturn $salesReturn,
        SalesReturnDetail $detail
    ): void {

        $this->stockService->saleReturn(
            product: $product,
            quantity: $detail->qty,
            referenceNo: $salesReturn->return_no,
            referenceId: $salesReturn->id,
            unitCost: $detail->unit_cost,
            remark: $detail->reason ?: 'Sales Return'
        );
    }

    /**
     * Recalculate Sale after return.
     *
     * Reserved for future:
     * - Net Sales
     * - Returned Amount
     * - Gross Profit
     * - Customer Balance
     * - Dashboard KPI
     */
    protected function recalculateSale(
        Sale $sale
    ): void {

        $sale->refresh();

        // Future implementation.
    }
    /**
     * Cancel a Sales Return.
     */
    public function cancel(
        SalesReturn $salesReturn
    ): SalesReturn {

        return DB::transaction(function () use ($salesReturn) {

            if ($salesReturn->isCancelled()) {
                throw new InvalidArgumentException(
                    "Sales Return {$salesReturn->return_no} is already cancelled."
                );
            }

            $salesReturn->loadMissing([
                'details.product',
                'sale',
            ]);

            foreach ($salesReturn->details as $detail) {

                $this->reverseStock(
                    product: $detail->product,
                    salesReturn: $salesReturn,
                    detail: $detail
                );
            }

            $salesReturn->update([
                'status' => 'Cancelled',
            ]);

            $this->recalculateSale(
                $salesReturn->sale
            );

            return $salesReturn->fresh([
                'details.product',
            ]);
        });
    }

    /**
     * Delete Draft Sales Return.
     */
    public function deleteDraft(
        SalesReturn $salesReturn
    ): void {

        DB::transaction(function () use ($salesReturn) {

            if (! $salesReturn->isDraft()) {
                throw new InvalidArgumentException(
                    'Only Draft Sales Returns can be deleted.'
                );
            }

            $salesReturn->details()->delete();

            $salesReturn->delete();
        });
    }

    /**
     * Reverse inventory when cancelling a Sales Return.
     */
    protected function reverseStock(
        Product $product,
        SalesReturn $salesReturn,
        SalesReturnDetail $detail
    ): void {

        $this->stockService->stockOut(
            product: $product,
            quantity: $detail->qty,
            referenceNo: $salesReturn->return_no,
            referenceId: $salesReturn->id,
            unitCost: $detail->unit_cost,
            movementType: 'Sales Return Cancel',
            remark: 'Sales Return Cancelled'
        );
    }

    /**
     * Remaining quantity available for return.
     */
    protected function remainingQty(
        SaleDetail $detail
    ): float {

        $returned = SalesReturnDetail::query()
            ->where(
                'sale_detail_id',
                $detail->id
            )
            ->sum('qty');

        return round(
            (float) $detail->qty - (float) $returned,
            2
        );
    }

    /**
     * Determine whether a Sale Detail can still be returned.
     */
    protected function canReturn(
        SaleDetail $detail,
        float $qty
    ): bool {

        return $qty <= $this->remainingQty($detail);
    }
}