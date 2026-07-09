<?php

use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\User;

function createReportSupplier(string $companyName): Supplier
{
    return Supplier::create([
        'supplier_code' => fake()->unique()->bothify('RPT-SUP-####'),
        'company_name' => $companyName,
        'status' => true,
    ]);
}

function createReportPurchase(User $user, Supplier $supplier, array $attributes = []): Purchase
{
    return Purchase::create(array_merge([
        'purchase_no' => fake()->unique()->bothify('RPT-PO-####'),
        'purchase_date' => now()->toDateString(),
        'supplier_id' => $supplier->id,
        'invoice_no' => fake()->unique()->bothify('RPT-INV-####'),
        'subtotal' => 500,
        'discount_percent' => 0,
        'discount_amount' => 0,
        'tax_percent' => 0,
        'tax_amount' => 0,
        'grand_total' => 500,
        'paid_amount' => 300,
        'balance' => 200,
        'status' => 'Completed',
        'created_by' => $user->id,
    ], $attributes));
}

test('guests are redirected from the purchase report', function () {
    $response = $this->get(route('reports.purchases'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view the purchase report', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->get(route('reports.purchases'));

    $response->assertOk()
        ->assertSee('Purchase Report')
        ->assertSee('Gross Total')
        ->assertSee('Net Purchase');
});

test('purchase report can be filtered by supplier and status', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $selectedSupplier = createReportSupplier('Selected CCTV Supplier');
    $otherSupplier = createReportSupplier('Other CCTV Supplier');

    $includedPurchase = createReportPurchase($user, $selectedSupplier, [
        'purchase_no' => 'RPT-PO-INCLUDED',
        'status' => 'Completed',
    ]);

    createReportPurchase($user, $selectedSupplier, [
        'purchase_no' => 'RPT-PO-DRAFT',
        'status' => 'Draft',
    ]);

    createReportPurchase($user, $otherSupplier, [
        'purchase_no' => 'RPT-PO-OTHER',
        'status' => 'Completed',
    ]);

    $response = $this->get(route('reports.purchases', [
        'supplier_id' => $selectedSupplier->id,
        'status' => 'Completed',
    ]));

    $response->assertOk()
        ->assertSee($includedPurchase->purchase_no)
        ->assertDontSee('RPT-PO-DRAFT')
        ->assertDontSee('RPT-PO-OTHER');
});

test('purchase report subtracts purchase returns from net totals', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $supplier = createReportSupplier('Return Net Supplier');
    $purchase = createReportPurchase($user, $supplier, [
        'purchase_no' => 'RPT-PO-NET',
        'grand_total' => 500,
        'paid_amount' => 300,
        'balance' => 200,
    ]);

    PurchaseReturn::create([
        'return_no' => 'RPT-RETURN-001',
        'return_date' => now()->toDateString(),
        'purchase_id' => $purchase->id,
        'supplier_id' => $supplier->id,
        'subtotal' => 125,
        'status' => 'Completed',
        'created_by' => $user->id,
    ]);

    $response = $this->get(route('reports.purchases', [
        'supplier_id' => $supplier->id,
        'status' => 'Completed',
    ]));

    $response->assertOk()
        ->assertSee('500.00')
        ->assertSee('125.00')
        ->assertSee('375.00');
});
