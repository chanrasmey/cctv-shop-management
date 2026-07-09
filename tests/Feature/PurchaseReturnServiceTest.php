<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Services\Inventory\PurchaseReturnService;
use App\Services\Inventory\PurchaseService;
use InvalidArgumentException;

function createReturnServiceProduct(array $attributes = []): Product
{
    $supplier = Supplier::create([
        'supplier_code' => fake()->unique()->bothify('RET-SUP-####'),
        'company_name' => fake()->company(),
        'status' => true,
    ]);

    $category = Category::create([
        'name' => fake()->word(),
        'code' => fake()->unique()->bothify('RET-CAT-####'),
        'status' => true,
    ]);

    $brand = Brand::create([
        'name' => fake()->company(),
        'code' => fake()->unique()->bothify('RET-BR-####'),
        'status' => true,
    ]);

    $unit = Unit::create([
        'name' => 'Piece',
        'short_name' => fake()->unique()->bothify('ret-pc-###'),
        'status' => true,
    ]);

    return Product::create(array_merge([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'unit_id' => $unit->id,
        'supplier_id' => $supplier->id,
        'sku' => fake()->unique()->bothify('RET-SKU-####'),
        'product_name' => fake()->words(3, true),
        'buy_price' => 0,
        'average_cost' => 0,
        'sell_price' => 150,
        'minimum_stock' => 1,
        'stock' => 0,
        'status' => true,
    ], $attributes));
}

function returnServicePurchasePayload(Product $product, string $status = 'Completed'): array
{
    return [
        'purchase_date' => now()->toDateString(),
        'supplier_id' => $product->supplier_id,
        'invoice_no' => fake()->unique()->bothify('RET-INV-####'),
        'status' => $status,
        'subtotal' => 100,
        'discount_percent' => 0,
        'discount_amount' => 0,
        'tax_percent' => 0,
        'tax_amount' => 0,
        'grand_total' => 100,
        'paid_amount' => 100,
        'balance' => 0,
        'product_id' => [$product->id],
        'qty' => [2],
        'unit_cost' => [50],
        'discount_percent_item' => [0],
        'discount_amount_item' => [0],
        'subtotal_item' => [100],
    ];
}

function returnServicePayload(int $purchaseDetailId, float $qty = 1): array
{
    return [
        'return_date' => now()->toDateString(),
        'remark' => 'Supplier accepted return',
        'purchase_detail_id' => [$purchaseDetailId],
        'qty' => [$qty],
        'reason' => ['Damaged item'],
    ];
}

test('completed purchases can be partially returned', function () {
    $this->actingAs(User::factory()->create());

    $product = createReturnServiceProduct();
    $purchase = app(PurchaseService::class)->create(
        returnServicePurchasePayload($product)
    );
    $detail = $purchase->details()->first();

    $purchaseReturn = app(PurchaseReturnService::class)->create(
        $purchase,
        returnServicePayload($detail->id, 1)
    );

    expect($purchaseReturn)->toBeInstanceOf(PurchaseReturn::class)
        ->and((float) $purchaseReturn->subtotal)->toBe(50.0)
        ->and($purchaseReturn->details)->toHaveCount(1)
        ->and((float) $product->fresh()->stock)->toBe(1.0)
        ->and(PurchaseReturnDetail::count())->toBe(1)
        ->and(StockMovement::where('reference_no', $purchaseReturn->return_no)
            ->where('movement_type', 'Purchase Return')
            ->count())->toBe(1);
});

test('draft purchases cannot be returned', function () {
    $this->actingAs(User::factory()->create());

    $product = createReturnServiceProduct();
    $purchase = app(PurchaseService::class)->create(
        returnServicePurchasePayload($product, 'Draft')
    );
    $detail = $purchase->details()->first();

    app(PurchaseReturnService::class)->create(
        $purchase,
        returnServicePayload($detail->id, 1)
    );
})->throws(InvalidArgumentException::class, 'Only Completed purchases can be returned.');

test('return quantity cannot exceed remaining purchased quantity', function () {
    $this->actingAs(User::factory()->create());

    $product = createReturnServiceProduct();
    $purchase = app(PurchaseService::class)->create(
        returnServicePurchasePayload($product)
    );
    $detail = $purchase->details()->first();

    app(PurchaseReturnService::class)->create(
        $purchase,
        returnServicePayload($detail->id, 2)
    );

    app(PurchaseReturnService::class)->create(
        $purchase,
        returnServicePayload($detail->id, 1)
    );
})->throws(InvalidArgumentException::class, 'Return quantity cannot exceed the remaining purchased quantity.');

test('return quantity cannot exceed current stock', function () {
    $this->actingAs(User::factory()->create());

    $product = createReturnServiceProduct();
    $purchase = app(PurchaseService::class)->create(
        returnServicePurchasePayload($product)
    );
    $detail = $purchase->details()->first();

    $product->update(['stock' => 0]);

    app(PurchaseReturnService::class)->create(
        $purchase,
        returnServicePayload($detail->id, 1)
    );
})->throws(InvalidArgumentException::class, 'Cannot return purchase because available stock is lower than the return quantity.');

test('return items must belong to the selected purchase', function () {
    $this->actingAs(User::factory()->create());

    $product = createReturnServiceProduct();
    $otherProduct = createReturnServiceProduct();

    $purchase = app(PurchaseService::class)->create(
        returnServicePurchasePayload($product)
    );
    $otherPurchase = app(PurchaseService::class)->create(
        returnServicePurchasePayload($otherProduct)
    );
    $otherDetail = $otherPurchase->details()->first();

    app(PurchaseReturnService::class)->create(
        $purchase,
        returnServicePayload($otherDetail->id, 1)
    );
})->throws(InvalidArgumentException::class, 'Return item does not belong to this purchase.');
