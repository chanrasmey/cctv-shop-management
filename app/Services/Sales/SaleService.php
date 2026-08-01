<?php

namespace App\Services\Sales;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Services\Inventory\StockService;
use App\Services\Shared\NumberGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class SaleService
{
    public function __construct(
        protected StockService $stockService,
    ) {
    }

    /**
     * Generate Sale Number.
     */
    protected function generateSaleNo(): string
    {
        return NumberGeneratorService::generate(
            'SAL',
            Sale::class,
            'sale_no'
        );
    }

    /**
     * Generate Invoice Number.
     */
    protected function generateInvoiceNo(): string
    {
        return NumberGeneratorService::generate(
            'INV',
            Sale::class,
            'invoice_no'
        );
    }

    /**
     * Ensure at least one item exists.
     *
     * @param array<string,mixed> $data
     */
    protected function ensureHasItems(array $data): void
    {
        if (
            ! isset($data['items']) ||
            ! is_array($data['items']) ||
            count($data['items']) === 0
        ) {
            throw ValidationException::withMessages([
                'items' => 'Please add at least one product.',
            ]);
        }
    }

    /**
     * Validate sale status.
     */
    protected function validateEditable(Sale $sale): void
    {
        if ($sale->status === 'Cancelled') {
            throw new InvalidArgumentException(
                'Cancelled sales cannot be modified.'
            );
        }

        if ($sale->status === 'Returned') {
            throw new InvalidArgumentException(
                'Returned sales cannot be modified.'
            );
        }
    }

    /**
     * Calculate sale totals.
     *
     * @param array<string,mixed> $data
     * @return array<string,float>
     */
    protected function calculateTotals(array $data): array
    {
        $subtotal = 0;

        foreach ($data['items'] as $item) {

            $qty = round((float) ($item['qty'] ?? 0), 2);

            $price = round((float) ($item['unit_price'] ?? 0), 2);

            $gross = $qty * $price;

            $lineDiscountPercent = (float) (
                $item['discount_percent'] ?? 0
            );

            $lineDiscountAmount = round(
                $gross * ($lineDiscountPercent / 100),
                2
            );

            $subtotal += $gross - $lineDiscountAmount;
        }

        $subtotal = round($subtotal, 2);

        $discountPercent = round(
            (float) ($data['discount_percent'] ?? 0),
            2
        );

        $discountAmount = round(
            $subtotal * ($discountPercent / 100),
            2
        );

        $taxable = $subtotal - $discountAmount;

        $taxPercent = round(
            (float) ($data['tax_percent'] ?? 0),
            2
        );

        $taxAmount = round(
            $taxable * ($taxPercent / 100),
            2
        );

        $grandTotal = round(
            $taxable + $taxAmount,
            2
        );

        $paidAmount = round(
            (float) ($data['paid_amount'] ?? 0),
            2
        );

        return [

            'subtotal' => $subtotal,

            'discount_percent' => $discountPercent,

            'discount_amount' => $discountAmount,

            'tax_percent' => $taxPercent,

            'tax_amount' => $taxAmount,

            'grand_total' => $grandTotal,

            'paid_amount' => $paidAmount,

            'balance' => max(
                round($grandTotal - $paidAmount, 2),
                0
            ),

            'change_amount' => max(
                round($paidAmount - $grandTotal, 2),
                0
            ),
        ];
    }

    /**
     * Create one sale detail.
     *
     * @param array<string,mixed> $item
     */
    protected function createSaleDetail(
        Sale $sale,
        Product $product,
        array $item
    ): SaleDetail {

        $qty = round((float) $item['qty'], 2);

        $price = round((float) $item['unit_price'], 2);

        $gross = $qty * $price;

        $discountPercent = round(
            (float) ($item['discount_percent'] ?? 0),
            2
        );

        $discountAmount = round(
            $gross * ($discountPercent / 100),
            2
        );

        $subtotal = round(
            $gross - $discountAmount,
            2
        );

        $unitCost = $this->stockService->getUnitCost(
            $product
        );

        $profit = round(
            $subtotal - ($qty * $unitCost),
            2
        );

        return $sale->details()->create([

            'product_id' => $product->id,

            'qty' => $qty,

            'unit_price' => $price,

            'unit_cost' => $unitCost,

            'discount_percent' => $discountPercent,

            'discount_amount' => $discountAmount,

            'subtotal' => $subtotal,

            'profit' => $profit,

            'remark' => $item['remark'] ?? null,

        ]);
    }

    // ===== PART 2 STARTS HERE =====
    /**
     * Store Sale.
     *
     * @param array<string,mixed> $data
     */
    public function store(array $data): Sale
    {
        return DB::transaction(function () use ($data) {

            $this->ensureHasItems($data);

            $totals = $this->calculateTotals($data);

            $sale = Sale::create([

                'sale_no' => $this->generateSaleNo(),

                'invoice_no' => $this->generateInvoiceNo(),

                'sale_date' => $data['sale_date'],

                'customer_id' => $data['customer_id'] ?? null,

                'subtotal' => $totals['subtotal'],

                'discount_percent' => $totals['discount_percent'],

                'discount_amount' => $totals['discount_amount'],

                'tax_percent' => $totals['tax_percent'],

                'tax_amount' => $totals['tax_amount'],

                'grand_total' => $totals['grand_total'],

                'paid_amount' => $totals['paid_amount'],

                'balance' => $totals['balance'],

                'change_amount' => $totals['change_amount'],

                'status' => 'Completed',

                'remark' => $data['remark'] ?? null,

                'created_by' => Auth::id(),

            ]);

            foreach ($data['items'] as $item) {

                /** @var Product $product */
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                $qty = round(
                    (float) $item['qty'],
                    2
                );

                if (
                    ! $this->stockService->hasAvailableStock(
                        $product,
                        $qty
                    )
                ) {
                    throw ValidationException::withMessages([
                        'stock' => sprintf(
                            '%s has only %.2f remaining.',
                            $product->product_name,
                            $product->stock
                        ),
                    ]);
                }

                $detail = $this->createSaleDetail(
                    $sale,
                    $product,
                    $item
                );

                $this->stockService->stockOut(

                    product: $product,

                    quantity: $detail->qty,

                    referenceNo: $sale->sale_no,

                    referenceId: $sale->id,

                    unitCost: $detail->unit_cost,

                    movementType: 'Sale',

                    remark: 'Sale Created'

                );
            }

            $sale->load([
                'customer',
                'details.product',
                'creator',
            ]);

            $this->audit(
                'created',
                $sale
            );

            $this->dispatchEvent(
                'sale.created',
                $sale
            );

            return $sale;
        });
    }
    /**
     * Update Sale.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        Sale $sale,
        array $data
    ): Sale {
        return DB::transaction(function () use ($sale, $data) {

            $sale->load([
                'details.product',
            ]);

            $this->validateEditable($sale);

            $this->ensureHasItems($data);

            /*
            |--------------------------------------------------------------------------
            | Restore Previous Inventory
            |--------------------------------------------------------------------------
            */

            foreach ($sale->details as $detail) {

                if (! $detail->product) {
                    continue;
                }

                /** @var Product $product */
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($detail->product_id);

                $this->stockService->stockIn(

                    product: $product,

                    quantity: (float) $detail->qty,

                    referenceNo: $sale->sale_no,

                    referenceId: $sale->id,

                    unitCost: (float) $detail->unit_cost,

                    movementType: 'Sale Update Restore',

                    remark: 'Restore inventory before sale update'

                );
            }

            /*
            |--------------------------------------------------------------------------
            | Remove Existing Detail Records
            |--------------------------------------------------------------------------
            */

            $sale->details()->delete();

            /*
            |--------------------------------------------------------------------------
            | Recalculate Totals
            |--------------------------------------------------------------------------
            */

            $totals = $this->calculateTotals($data);

            /*
            |--------------------------------------------------------------------------
            | Update Header
            |--------------------------------------------------------------------------
            */

            $sale->update([

                'sale_date' => $data['sale_date'],

                'customer_id' => $data['customer_id'] ?? null,

                'subtotal' => $totals['subtotal'],

                'discount_percent' => $totals['discount_percent'],

                'discount_amount' => $totals['discount_amount'],

                'tax_percent' => $totals['tax_percent'],

                'tax_amount' => $totals['tax_amount'],

                'grand_total' => $totals['grand_total'],

                'paid_amount' => $totals['paid_amount'],

                'balance' => $totals['balance'],

                'change_amount' => $totals['change_amount'],

                'remark' => $data['remark'] ?? null,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Save New Detail Records
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] as $item) {

                /** @var Product $product */
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($item['product_id']);

                $qty = round(
                    (float) $item['qty'],
                    2
                );

                if (
                    ! $this->stockService->hasAvailableStock(
                        $product,
                        $qty
                    )
                ) {
                    throw ValidationException::withMessages([
                        'stock' => sprintf(
                            '%s has only %.2f remaining.',
                            $product->product_name,
                            $product->stock
                        ),
                    ]);
                }

                $detail = $this->createSaleDetail(
                    $sale,
                    $product,
                    $item
                );

                $this->stockService->stockOut(

                    product: $product,

                    quantity: (float) $detail->qty,

                    referenceNo: $sale->sale_no,

                    referenceId: $sale->id,

                    unitCost: (float) $detail->unit_cost,

                    movementType: 'Sale',

                    remark: 'Sale Updated'

                );
            }

            $sale->load([
                'customer',
                'details.product',
                'creator',
            ]);

            $this->audit(
                'updated',
                $sale
            );

            $this->dispatchEvent(
                'sale.updated',
                $sale
            );

            return $sale;
        });
    }
    /**
     * Delete Sale.
     */
    public function delete(Sale $sale): void
    {
        DB::transaction(function () use ($sale) {

            $sale->load([
                'details.product',
            ]);

            $this->validateEditable($sale);

            foreach ($sale->details as $detail) {

                if (! $detail->product) {
                    continue;
                }

                /** @var Product $product */
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($detail->product_id);

                $this->stockService->stockIn(

                    product: $product,

                    quantity: (float) $detail->qty,

                    referenceNo: $sale->sale_no,

                    referenceId: $sale->id,

                    unitCost: (float) $detail->unit_cost,

                    movementType: 'Sale Delete',

                    remark: 'Restore inventory after sale deletion'

                );
            }

            $sale->details()->delete();

            $sale->delete();

            $this->audit(
                'deleted',
                $sale
            );

            $this->dispatchEvent(
                'sale.deleted',
                $sale
            );
        });
    }

    /**
     * Cancel Sale.
     */
    public function cancel(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {

            $sale->load([
                'details.product',
            ]);

            if ($sale->status === 'Cancelled') {
                throw new InvalidArgumentException(
                    'Sale is already cancelled.'
                );
            }

            foreach ($sale->details as $detail) {

                if (! $detail->product) {
                    continue;
                }

                /** @var Product $product */
                $product = Product::query()
                    ->lockForUpdate()
                    ->findOrFail($detail->product_id);

                $this->stockService->stockIn(

                    product: $product,

                    quantity: (float) $detail->qty,

                    referenceNo: $sale->sale_no,

                    referenceId: $sale->id,

                    unitCost: (float) $detail->unit_cost,

                    movementType: 'Sale Cancel',

                    remark: 'Sale Cancelled'

                );
            }

            $sale->update([
                'status' => 'Cancelled',
            ]);

            $sale->load([
                'customer',
                'details.product',
                'creator',
            ]);

            $this->audit(
                'cancelled',
                $sale
            );

            $this->dispatchEvent(
                'sale.cancelled',
                $sale
            );

            return $sale;
        });
    }

    /**
     * Audit hook.
     *
     * Reserved for future Activity Log implementation.
     */
    protected function audit(
        string $action,
        Sale $sale,
        array $context = []
    ): void {
        // Future Activity Log integration.
    }

    /**
     * Domain Event hook.
     *
     * Reserved for future Event implementation.
     */
    protected function dispatchEvent(
        string $event,
        Sale $sale,
        array $payload = []
    ): void {
        // Future Domain Events.
    }
}