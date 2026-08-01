<?php

namespace App\Services\Sales;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SalesReturn;
use App\Models\SalesReturnDetail;
use App\Services\Inventory\StockService;
use App\Services\Shared\NumberGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class SalesReturnService
{
    public function __construct(
        protected StockService $stockService
    ) {
    }

    /**
     * Generate Sales Return Number.
     */
    public function generateReturnNumber(): string
    {
        return NumberGeneratorService::generate(
            'SR',
            SalesReturn::class,
            'return_no'
        );
    }

    /**
     * Store Sales Return.
     *
     * @throws Throwable
     */
    public function store(array $data): SalesReturn
    {
        return DB::transaction(function () use ($data) {

            $this->validateReturn($data);

            $salesReturn = $this->createHeader($data);

            $this->createDetails(
                $salesReturn,
                $data
            );

            $salesReturn->recalculateTotals();

            $salesReturn->load([
                'sale',
                'customer',
                'details.product',
            ]);

            $this->afterCreated(
                $salesReturn
            );

            return $salesReturn;
        });
    }

    /**
     * Cancel Sales Return.
     *
     * @throws Throwable
     */
    public function cancel(
        SalesReturn $salesReturn
    ): void {

        DB::transaction(function () use ($salesReturn) {

            if ($salesReturn->isCancelled()) {
                return;
            }

            $salesReturn->load(
                'details.product'
            );

            if ($salesReturn->isCompleted()) {

                $this->rollbackInventory(
                    $salesReturn
                );

            }

            $salesReturn->update([
                'status' => 'Cancelled',
            ]);

            $this->afterCancelled(
                $salesReturn
            );

        });

    }

    /**
     * Delete Sales Return.
     *
     * @throws Throwable
     */
    public function delete(
        SalesReturn $salesReturn
    ): void {

        DB::transaction(function () use ($salesReturn) {

            $salesReturn->load(
                'details.product'
            );

            if ($salesReturn->isCompleted()) {

                $this->rollbackInventory(
                    $salesReturn
                );

            }

            $salesReturn->details()->delete();

            $this->afterDeleted(
                $salesReturn
            );

            $salesReturn->delete();

        });

    }

    /**
     * Validate Sales Return.
     */
    protected function validateReturn(
        array $data
    ): void {

        if (
            empty($data['sale_id'])
        ) {

            throw ValidationException::withMessages([
                'sale_id' =>
                    'Please select a Sale.',
            ]);

        }

        $sale = Sale::with('details')
            ->findOrFail(
                $data['sale_id']
            );

        if (! $sale->isCompleted()) {

            throw ValidationException::withMessages([
                'sale_id' =>
                    'Only completed sales can be returned.',
            ]);

        }

        if (
            empty($data['sale_detail_id']) ||
            ! is_array($data['sale_detail_id'])
        ) {

            throw ValidationException::withMessages([
                'sale_detail_id' =>
                    'Please select at least one item.',
            ]);

        }

        foreach (
            $data['sale_detail_id'] as $index => $detailId
        ) {

            /** @var SaleDetail $detail */
            $detail = SaleDetail::findOrFail(
                $detailId
            );

            $qty = $this->toDecimal(
                $data['qty'][$index] ?? 0
            );

            if ($qty <= 0) {

                throw ValidationException::withMessages([
                    "qty.$index" =>
                        'Return quantity must be greater than zero.',
                ]);

            }

            $alreadyReturned = SalesReturnDetail::query()
                ->where('sale_detail_id', $detail->id)
                ->whereHas('salesReturn', function ($query) {

                    $query->where(
                        'status',
                        'Completed'
                    );

                })
                ->sum('qty');

            $remaining = round(
                $detail->qty - $alreadyReturned,
                2
            );

            if ($qty > $remaining) {

                throw ValidationException::withMessages([

                    "qty.$index" => sprintf(
                        'Maximum return quantity is %.2f.',
                        $remaining
                    ),

                ]);

            }

        }

    }
        /**
     * Create Sales Return header.
     */
    protected function createHeader(
        array $data
    ): SalesReturn {

        $sale = Sale::with('customer')
            ->findOrFail(
                $data['sale_id']
            );

        return SalesReturn::create([

            'return_no' => $this->generateReturnNumber(),

            'return_date' => $data['return_date'],

            'sale_id' => $sale->id,

            'customer_id' => $sale->customer_id,

            'subtotal' => 0,

            'remark' => $data['remark'] ?? null,

            'status' => $data['status'] ?? 'Completed',

            'created_by' => Auth::id(),

        ]);

    }

    /**
     * Reload and recalculate return totals.
     */
    protected function recalculateReturn(
        SalesReturn $salesReturn
    ): void {

        $salesReturn->load('details');

        $salesReturn->recalculateTotals();

    }

    /**
     * Recalculate the parent Sale if required.
     *
     * Reserved for future enhancements such as:
     * - Returned amount
     * - Refund amount
     * - Partial return status
     * - Fully returned status
     */
    protected function recalculateSale(
        Sale $sale
    ): void {

        // Reserved for ERP v1.5

    }
        /**
     * Create Sales Return detail records.
     */
    protected function createDetails(
        SalesReturn $salesReturn,
        array $data
    ): void {

        $saleDetails = SaleDetail::query()
            ->whereIn('id', $data['sale_detail_id'])
            ->with('product')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($data['sale_detail_id'] as $index => $detailId) {

            /** @var SaleDetail|null $saleDetail */
            $saleDetail = $saleDetails->get($detailId);

            if (! $saleDetail) {

                throw new InvalidArgumentException(
                    "Sale Detail {$detailId} does not exist."
                );

            }

            $product = $saleDetail->product;

            if (! $product) {

                throw new InvalidArgumentException(
                    "Product not found for Sale Detail {$detailId}."
                );

            }

            $qty = $this->toDecimal(
                $data['qty'][$index] ?? 0
            );

            $unitPrice = $this->toDecimal(
                $saleDetail->unit_price
            );

            $unitCost = $this->toDecimal(
                $saleDetail->unit_cost
            );

            $subtotal = round(
                $qty * $unitPrice,
                2
            );

            $profitAdjustment = round(
                ($unitPrice - $unitCost) * $qty,
                2
            );

            SalesReturnDetail::create([

                'sales_return_id' => $salesReturn->id,

                'sale_detail_id' => $saleDetail->id,

                'product_id' => $product->id,

                'qty' => $qty,

                'unit_price' => $unitPrice,

                'unit_cost' => $unitCost,

                'subtotal' => $subtotal,

                'profit_adjustment' => $profitAdjustment,

                'reason' => $data['reason'][$index] ?? null,

                'remark' => $data['remark_item'][$index] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Draft Return
            |--------------------------------------------------------------------------
            */

            if (! $salesReturn->isCompleted()) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Restore Inventory
            |--------------------------------------------------------------------------
            */

            $this->stockService->stockIn(

                product: $product,

                quantity: $qty,

                referenceNo: $salesReturn->return_no,

                referenceId: $salesReturn->id,

                unitCost: $unitCost,

                movementType: 'Sales Return',

                remark: 'Sales Return Stock In'

            );

        }

        $this->recalculateReturn(
            $salesReturn
        );

        $this->recalculateSale(
            $salesReturn->sale
        );

    }

    /**
     * Roll back inventory when cancelling or deleting
     * a completed Sales Return.
     */
    protected function rollbackInventory(
        SalesReturn $salesReturn
    ): void {

        foreach ($salesReturn->details as $detail) {

            $product = Product::lockForUpdate()
                ->findOrFail(
                    $detail->product_id
                );

            $this->stockService->stockOut(

                product: $product,

                quantity: (float) $detail->qty,

                referenceNo: $salesReturn->return_no,

                referenceId: $salesReturn->id,

                unitCost: (float) $detail->unit_cost,

                movementType: 'Sales Return Rollback',

                remark: 'Rollback Sales Return'

            );

        }

    }
        /**
     * Convert value to decimal safely.
     */
    protected function toDecimal(
        mixed $value,
        int $precision = 2
    ): float {

        if ($value === null || $value === '') {
            return 0.0;
        }

        return round(
            (float) $value,
            $precision
        );

    }

    /**
     * Hook after creating a Sales Return.
     */
    protected function afterCreated(
        SalesReturn $salesReturn
    ): void {

        $this->audit(
            'created',
            $salesReturn
        );

    }

    /**
     * Hook after cancelling a Sales Return.
     */
    protected function afterCancelled(
        SalesReturn $salesReturn
    ): void {

        $this->audit(
            'cancelled',
            $salesReturn
        );

    }

    /**
     * Hook after deleting a Sales Return.
     */
    protected function afterDeleted(
        SalesReturn $salesReturn
    ): void {

        $this->audit(
            'deleted',
            $salesReturn
        );

    }

    /**
     * ERP audit hook.
     *
     * Reserved for:
     * - Activity Log
     * - Accounting Journal
     * - Notifications
     * - Event Broadcasting
     * - Webhooks
     */
    protected function audit(
        string $action,
        SalesReturn $salesReturn
    ): void {

        // Reserved for ERP v1.x

    }
}
