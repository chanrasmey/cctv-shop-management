<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Services\Shared\NumberGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

class SaleService
{
    public function __construct(
        protected StockService $stockService,
        protected CalculationService $calculationService
    ) {
    }

    /**
     * Generate Sale Number.
     */
    public function generateSaleNumber(): string
    {
        return NumberGeneratorService::generate(
            'SI',
            Sale::class,
            'sale_no'
        );
    }

    /**
     * Create Sale Invoice.
     *
     * @throws Throwable
     */
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {

            $this->validateSale($data);

            $sale = $this->createSaleHeader($data);

            $this->createSaleDetails(
                $sale,
                $data
            );

            $sale->load([
                'customer',
                'details.product',
                'creator',
            ]);

            $this->afterSaleCreated($sale);

            return $sale;
        });
    }

    /**
     * Update Sale.
     *
     * Future-ready for editing invoices.
     *
     * @throws Throwable
     */
    public function update(
        Sale $sale,
        array $data
    ): Sale {

        return DB::transaction(function () use (
            $sale,
            $data
        ) {

            $sale->load('details.product');

            $this->validateSale($data);

            if ($sale->isCompleted()) {

                $this->rollbackInventory(
                    $sale
                );

            }

            $sale->details()->delete();

            $sale->update(
                $this->extractHeaderData($data)
            );

            $this->createSaleDetails(
                $sale,
                $data
            );

            $sale->load([
                'customer',
                'details.product',
                'creator',
            ]);

            $this->afterSaleUpdated($sale);

            return $sale;

        });

    }

    /**
     * Delete Sale.
     *
     * @throws Throwable
     */
    public function delete(
        Sale $sale
    ): void {

        DB::transaction(function () use ($sale) {

            $sale->load('details.product');

            if ($sale->isCompleted()) {

                $this->rollbackInventory(
                    $sale
                );

            }

            $sale->details()->delete();

            $this->afterSaleDeleted(
                $sale
            );

            $sale->delete();

        });

    }

    /**
     * Roll back inventory before updating/deleting.
     */
    protected function rollbackInventory(
        Sale $sale
    ): void {

        foreach ($sale->details as $detail) {

            $product = Product::lockForUpdate()
                ->findOrFail(
                    $detail->product_id
                );

            $this->stockService->stockIn(

                product: $product,

                quantity: (float) $detail->qty,

                referenceNo: $sale->sale_no,

                referenceId: $sale->id,

                unitCost: (float) $detail->unit_cost,

                movementType: 'Sale Rollback',

                remark: 'Rollback Sale'

            );

        }

    }

    /**
     * Validate sale request.
     */
    protected function validateSale(
        array $data
    ): void {

        if (
            ! isset($data['product_id']) ||
            count($data['product_id']) === 0
        ) {

            throw ValidationException::withMessages([
                'product_id' =>
                    'Please add at least one product.',
            ]);

        }

        $required = [
            'product_id',
            'qty',
            'unit_price',
        ];

        foreach ($required as $field) {

            if (
                ! isset($data[$field]) ||
                ! is_array($data[$field])
            ) {

                throw ValidationException::withMessages([
                    $field =>
                        "{$field} is required.",
                ]);

            }

        }

        if (
            count($data['product_id'])
            !==
            count($data['qty'])
        ) {

            throw ValidationException::withMessages([
                'qty' =>
                    'Sale item data is inconsistent.',
            ]);

        }

        foreach (
            $data['product_id']
            as $index => $productId
        ) {

            $product = Product::lockForUpdate()
                ->findOrFail($productId);

            $qty = $this->toDecimal(
                $data['qty'][$index] ?? 0
            );

            if ($qty <= 0) {

                throw ValidationException::withMessages([
                    "qty.$index" =>
                        'Quantity must be greater than zero.',
                ]);

            }

            if (
                ($data['status'] ?? 'Draft')
                !==
                'Completed'
            ) {
                continue;
            }

            if (
                ! $this->hasSufficientStock(
                    $product,
                    $qty
                )
            ) {

                throw ValidationException::withMessages([

                    "qty.$index" => sprintf(
                        '%s only has %.2f remaining.',
                        $product->product_name,
                        $product->stock
                    ),

                ]);

            }

        }

    }
    /**
     * Create sale header.
     */
    protected function createSaleHeader(
        array $data
    ): Sale {

        $saleNo = $this->generateSaleNumber();

        return Sale::create(array_merge(

            [

                'sale_no' => $saleNo,

                'invoice_no' => $data['invoice_no'] ?? $saleNo,

                'created_by' => Auth::id(),

            ],

            $this->extractHeaderData($data)

        ));

    }

    /**
     * Extract sale header values.
     */
    protected function extractHeaderData(
        array $data
    ): array {

        $subtotal = $this->toDecimal(
            $data['subtotal'] ?? 0
        );

        $discountPercent = $this->toDecimal(
            $data['discount_percent'] ?? 0
        );

        $discountAmount = $this->toDecimal(
            $data['discount_amount'] ?? 0
        );

        $taxPercent = $this->toDecimal(
            $data['tax_percent'] ?? 0
        );

        $taxAmount = $this->toDecimal(
            $data['tax_amount'] ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Calculation Service
        |--------------------------------------------------------------------------
        */

        if (
            method_exists(
                $this->calculationService,
                'calculateSaleTotals'
            )
        ) {

            $totals = $this->calculationService
                ->calculateSaleTotals(

                    subtotal: $subtotal,

                    discountPercent: $discountPercent,

                    discountAmount: $discountAmount,

                    taxPercent: $taxPercent,

                    taxAmount: $taxAmount

                );

            $subtotal = $totals['subtotal'];

            $discountAmount = $totals['discount_amount'];

            $taxAmount = $totals['tax_amount'];

            $grandTotal = $totals['grand_total'];

        } else {

            if (
                $discountAmount <= 0 &&
                $discountPercent > 0
            ) {

                $discountAmount = round(
                    $subtotal *
                    ($discountPercent / 100),
                    2
                );

            }

            $afterDiscount = round(
                $subtotal - $discountAmount,
                2
            );

            if (
                $taxAmount <= 0 &&
                $taxPercent > 0
            ) {

                $taxAmount = round(
                    $afterDiscount *
                    ($taxPercent / 100),
                    2
                );

            }

            $grandTotal = round(
                $afterDiscount + $taxAmount,
                2
            );

        }

        $paidAmount = $this->toDecimal(
            $data['paid_amount'] ?? 0
        );

        $balance = max(
            $grandTotal - $paidAmount,
            0
        );

        $changeAmount = max(
            $paidAmount - $grandTotal,
            0
        );

        return [

            'sale_date' => $data['sale_date'],

            'customer_id' =>
                $data['customer_id'] ?? null,

            'invoice_no' =>
                $data['invoice_no'] ?? null,

            'subtotal' => $subtotal,

            'discount_percent' => $discountPercent,

            'discount_amount' => $discountAmount,

            'tax_percent' => $taxPercent,

            'tax_amount' => $taxAmount,

            'grand_total' => $grandTotal,

            'paid_amount' => $paidAmount,

            'balance' => $balance,

            'change_amount' => $changeAmount,

            'remark' => $data['remark'] ?? null,

            'status' => $data['status'] ?? 'Draft',

        ];

    }
    /**
     * Create all sale detail records.
     */
    protected function createSaleDetails(
        Sale $sale,
        array $data
    ): void {

        $products = Product::query()
            ->whereIn('id', $data['product_id'])
            ->lockForUpdate()
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

            $qty = $this->toDecimal(
                $data['qty'][$index] ?? 0
            );

            $unitPrice = $this->toDecimal(
                $data['unit_price'][$index] ?? 0
            );

            if ($unitPrice < 0) {

                throw ValidationException::withMessages([
                    "unit_price.$index" =>
                        'Unit price cannot be negative.',
                ]);

            }

            $unitCost = $this->resolveUnitCost(
                $product
            );

            $discountPercent = $this->toDecimal(
                $data['discount_percent_item'][$index] ?? 0
            );

            $discountAmount = $this->toDecimal(
                $data['discount_amount_item'][$index] ?? 0
            );

            $lineSubtotal = round(
                $qty * $unitPrice,
                2
            );

            if (
                $discountAmount <= 0 &&
                $discountPercent > 0
            ) {

                $discountAmount = round(
                    $lineSubtotal *
                    ($discountPercent / 100),
                    2
                );

            }

            $subtotal = round(
                $lineSubtotal - $discountAmount,
                2
            );

            $costTotal = round(
                $qty * $unitCost,
                2
            );

            $profit = round(
                $subtotal - $costTotal,
                2
            );

            SaleDetail::create([

                'sale_id' => $sale->id,

                'product_id' => $product->id,

                'qty' => $qty,

                'unit_price' => $unitPrice,

                'unit_cost' => $unitCost,

                'discount_percent' => $discountPercent,

                'discount_amount' => $discountAmount,

                'subtotal' => $subtotal,

                'profit' => $profit,

                'remark' =>
                    $data['remark_item'][$index] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Draft Sale
            |--------------------------------------------------------------------------
            */

            if (! $sale->isCompleted()) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Reduce Inventory
            |--------------------------------------------------------------------------
            */

            $this->stockService->stockOut(

                product: $product,

                quantity: $qty,

                referenceNo: $sale->sale_no,

                referenceId: $sale->id,

                unitCost: $unitCost,

                movementType: 'Sale',

                remark: 'Sales Stock Out'

            );

        }

    }

    /**
     * Resolve inventory cost.
     *
     * Future compatible with:
     * - FIFO
     * - LIFO
     * - Batch Cost
     * - Warehouse Cost
     */
    protected function resolveUnitCost(
        Product $product
    ): float {

        if (
            ! is_null($product->average_cost) &&
            $product->average_cost > 0
        ) {

            return $this->toDecimal(
                $product->average_cost
            );

        }

        if (
            isset($product->last_cost) &&
            $product->last_cost > 0
        ) {

            return $this->toDecimal(
                $product->last_cost
            );

        }

        return $this->toDecimal(
            $product->buy_price
        );

    }
    /**
     * Convert numeric values safely.
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
     * Check stock availability.
     */
    protected function hasSufficientStock(
        Product $product,
        float $qty
    ): bool {

        return $product->stock >= $qty;

    }

    /**
     * Hook executed after a sale is created.
     *
     * Reserved for:
     * - Notifications
     * - Accounting integration
     * - Loyalty points
     * - Event dispatching
     */
    protected function afterSaleCreated(
        Sale $sale
    ): void {

        $this->audit(
            'created',
            $sale
        );

    }

    /**
     * Hook executed after a sale is updated.
     */
    protected function afterSaleUpdated(
        Sale $sale
    ): void {

        $this->audit(
            'updated',
            $sale
        );

    }

    /**
     * Hook executed after a sale is deleted.
     */
    protected function afterSaleDeleted(
        Sale $sale
    ): void {

        $this->audit(
            'deleted',
            $sale
        );

    }

    /**
     * Audit placeholder.
     *
     * Future integrations:
     * - Activity Log
     * - ERP Audit Trail
     * - Event Broadcasting
     */
    protected function audit(
        string $action,
        Sale $sale
    ): void {

        // Intentionally left blank for v1.0

    }
}