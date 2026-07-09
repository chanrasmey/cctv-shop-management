<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Services\Inventory\PurchaseService;
use InvalidArgumentException;

function createPurchasableProduct(array $attributes = []): Product
{
    $supplier = Supplier::create([
        'supplier_code' => fake()->unique()->bothify('SUP-####'),
        'company_name' => fake()->company(),
        'status' => true,
    ]);

    $category = Category::create([
        'name' => fake()->word(),
        'code' => fake()->unique()->bothify('CAT-####'),
        'status' => true,
    ]);

    $brand = Brand::create([
        'name' => fake()->company(),
        'code' => fake()->unique()->bothify('BR-####'),
        'status' => true,
    ]);

    $unit = Unit::create([
        'name' => 'Piece',
        'short_name' => fake()->unique()->bothify('pc-###'),
        'status' => true,
    ]);

    return Product::create(array_merge([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'unit_id' => $unit->id,
        'supplier_id' => $supplier->id,
        'sku' => fake()->unique()->bothify('SKU-####'),
        'product_name' => fake()->words(3, true),
        'buy_price' => 0,
        'average_cost' => 0,
        'sell_price' => 150,
        'minimum_stock' => 1,
        'stock' => 0,
        'status' => true,
    ], $attributes));
}

function purchasePayload(Product $product, string $status = 'Draft', array $overrides = []): array
{
    return array_merge([
        'purchase_date' => now()->toDateString(),
        'supplier_id' => $product->supplier_id,
        'invoice_no' => 'INV-001',
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
    ], $overrides);
}

test('draft purchases save details without receiving stock', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Draft')
    );

    expect($purchase->status)->toBe('Draft')
        ->and($purchase->details)->toHaveCount(1)
        ->and((float) $product->fresh()->stock)->toBe(0.0)
        ->and(StockMovement::count())->toBe(0);
});

test('completed purchases receive stock and update cost', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Completed')
    );

    $product->refresh();

    expect($purchase->status)->toBe('Completed')
        ->and((float) $product->stock)->toBe(2.0)
        ->and((float) $product->buy_price)->toBe(50.0)
        ->and((float) $product->average_cost)->toBe(50.0)
        ->and(StockMovement::where('reference_no', $purchase->purchase_no)->count())->toBe(1);
});

test('draft purchases can be updated without receiving stock', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Draft')
    );

    $updated = app(PurchaseService::class)->update(
        $purchase,
        purchasePayload($product, 'Draft', [
            'invoice_no' => 'INV-UPDATED',
            'subtotal' => 150,
            'grand_total' => 150,
            'paid_amount' => 50,
            'balance' => 100,
            'qty' => [3],
            'unit_cost' => [50],
            'subtotal_item' => [150],
        ])
    );

    expect($updated->invoice_no)->toBe('INV-UPDATED')
        ->and($updated->details)->toHaveCount(1)
        ->and((float) $updated->details->first()->qty)->toBe(3.0)
        ->and((float) $product->fresh()->stock)->toBe(0.0)
        ->and(StockMovement::count())->toBe(0);
});

test('draft purchases can be completed during update', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Draft')
    );

    $updated = app(PurchaseService::class)->update(
        $purchase,
        purchasePayload($product, 'Completed')
    );

    expect($updated->status)->toBe('Completed')
        ->and((float) $product->fresh()->stock)->toBe(2.0)
        ->and(StockMovement::where('reference_no', $updated->purchase_no)->count())->toBe(1);
});

test('completed purchases cannot be updated', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Completed')
    );

    app(PurchaseService::class)->update(
        $purchase,
        purchasePayload($product, 'Draft')
    );
})->throws(InvalidArgumentException::class, 'Only Draft or Pending purchases can be updated.');



test('draft purchases can be cancelled without changing stock', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Draft')
    );

    $cancelled = app(PurchaseService::class)->cancel($purchase);

    expect($cancelled->status)->toBe('Cancelled')
        ->and($cancelled->details)->toHaveCount(1)
        ->and((float) $product->fresh()->stock)->toBe(0.0)
        ->and(StockMovement::count())->toBe(0);
});

test('completed purchases can be cancelled and reverse stock', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Completed')
    );

    $cancelled = app(PurchaseService::class)->cancel($purchase);
    $returnMovement = StockMovement::where('reference_no', $purchase->purchase_no)
        ->where('movement_type', 'Purchase Return')
        ->first();

    expect($cancelled->status)->toBe('Cancelled')
        ->and((float) $product->fresh()->stock)->toBe(0.0)
        ->and(StockMovement::where('reference_no', $purchase->purchase_no)->count())->toBe(2)
        ->and($returnMovement)->not->toBeNull()
        ->and((float) $returnMovement->qty_out)->toBe(2.0);
});

test('completed purchases cannot be cancelled when stock is lower than purchased quantity', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Completed')
    );

    $product->update(['stock' => 1]);

    app(PurchaseService::class)->cancel($purchase);
})->throws(
    InvalidArgumentException::class,
    'Cannot cancel purchase because available stock is lower than the purchased quantity.'
);

test('cancelled purchases cannot be cancelled again', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Draft')
    );

    $cancelled = app(PurchaseService::class)->cancel($purchase);

    app(PurchaseService::class)->cancel($cancelled);
})->throws(InvalidArgumentException::class, 'Purchase is already cancelled.');


test('draft purchases can be deleted', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Draft')
    );

    app(PurchaseService::class)->deleteDraft($purchase);

    expect(Purchase::count())->toBe(0)
        ->and(PurchaseDetail::count())->toBe(0)
        ->and((float) $product->fresh()->stock)->toBe(0.0);
});

test('completed purchases cannot be deleted as drafts', function () {
    $this->actingAs(User::factory()->create());

    $product = createPurchasableProduct();
    $purchase = app(PurchaseService::class)->create(
        purchasePayload($product, 'Completed')
    );

    app(PurchaseService::class)->deleteDraft($purchase);
})->throws(InvalidArgumentException::class, 'Only Draft purchases can be deleted.');
