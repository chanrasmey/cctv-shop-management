<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Services\Shared\NumberGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SaleService
{
    public function __construct(
        protected StockService $stockService
    ) {}

    public function generateSaleNumber(): string
    {
        return NumberGeneratorService::generate(
            'SI',
            Sale::class,
            'sale_no'
        );
    }

    /**
     * Create a sale invoice.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $this->ensureHasProducts($data);

            if (($data['status'] ?? 'Draft') === 'Completed') {
                $this->ensureStockAvailable($data);
            }

            $sale = Sale::create(array_merge(
                [
                    'sale_no' => $this->generateSaleNumber(),
                    'created_by' => Auth::id(),
                ],
                $this->saleAttributes($data)
            ));

            $this->saveDetails($sale, $data);

            return $sale->fresh(['customer', 'details.product', 'creator']);
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
     */
    private function ensureStockAvailable(array $data): void
    {
        foreach ($data['product_id'] as $index => $productId) {
            $product = Product::findOrFail($productId);
            $qty = (float) $data['qty'][$index];

            if ((float) $product->stock < $qty) {
                throw new InvalidArgumentException(
                    'Insufficient stock for '.$product->product_name.'.'
                );
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function saleAttributes(array $data): array
    {
        return [
            'sale_date' => $data['sale_date'],
            'customer_id' => $data['customer_id'] ?? null,
            'invoice_no' => $data['invoice_no'] ?? null,
            'subtotal' => $data['subtotal'] ?? 0,
            'discount_percent' => $data['discount_percent'] ?? 0,
            'discount_amount' => $data['discount_amount'] ?? 0,
            'tax_percent' => $data['tax_percent'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
            'paid_amount' => $data['paid_amount'] ?? 0,
            'balance' => $data['balance'] ?? 0,
            'change_amount' => $data['change_amount'] ?? 0,
            'remark' => $data['remark'] ?? null,
            'status' => $data['status'] ?? 'Draft',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function saveDetails(Sale $sale, array $data): void
    {
        foreach ($data['product_id'] as $index => $productId) {
            $product = Product::findOrFail($productId);
            $qty = (float) $data['qty'][$index];
            $unitPrice = (float) $data['unit_price'][$index];
            $unitCost = (float) ($product->average_cost ?: $product->buy_price);
            $subtotal = (float) $data['subtotal_item'][$index];
            $profit = $subtotal - ($qty * $unitCost);

            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'unit_cost' => $unitCost,
                'discount_percent' =>
                    $data['discount_percent_item'][$index] ?? 0,
                'discount_amount' =>
                    $data['discount_amount_item'][$index] ?? 0,
                'subtotal' => $subtotal,
                'profit' => round($profit, 2),
                'remark' => null,
            ]);

            if ($sale->isCompleted()) {
                $this->stockService->stockOut(
                    $product,
                    $qty,
                    $sale->sale_no,
                    $sale->id,
                    $unitCost,
                    'Sales Stock Out'
                );
            }
        }
    }
}
