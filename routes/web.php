<?php


use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

});

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Master Data
    |--------------------------------------------------------------------------
    */

    Route::resource('categories', CategoryController::class);

    Route::resource('brands', BrandController::class);

    Route::resource('units', UnitController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::resource('customers', CustomerController::class);

    Route::resource('products', ProductController::class);

    Route::get('/products/{product}/barcode', [ProductController::class, 'barcode'])
        ->name('products.barcode');


    /*
    |--------------------------------------------------------------------------
    | Company Settings
    |--------------------------------------------------------------------------
    */

    Route::get(
    '/settings/company',
    [App\Http\Controllers\CompanySettingController::class, 'edit']
)
->name('settings.company.edit');

Route::put(
    '/settings/company',
    [App\Http\Controllers\CompanySettingController::class, 'update']
)
->name('settings.company.update');   

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    Route::get('/purchases/{purchase}/returns/create', [PurchaseReturnController::class, 'create'])
        ->name('purchases.returns.create');

    Route::post('/purchases/{purchase}/returns', [PurchaseReturnController::class, 'store'])
        ->name('purchases.returns.store');

    Route::get('/purchase-returns/{purchaseReturn}', [PurchaseReturnController::class, 'show'])
        ->name('purchase-returns.show');

    Route::patch('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])
        ->name('purchases.cancel');

    Route::resource('purchases', PurchaseController::class);

    /*
|--------------------------------------------------------------------------
| Sales
|--------------------------------------------------------------------------
*/

Route::resource('sales', SalesController::class);

Route::get(
    'sales/{sale}/print',
    [SalesController::class, 'print']
)->name('sales.print');

Route::post(
    'sales/{sale}/duplicate',
    [SalesController::class, 'duplicate']
)->name('sales.duplicate');
/*
|--------------------------------------------------------------------------
| Sales Returns
|--------------------------------------------------------------------------
*/

Route::resource(
    'sales-returns',
    SalesReturnController::class
);

Route::get(
    'sales-returns/{salesReturn}/print',
    [SalesReturnController::class, 'print']
)->name('sales-returns.print');

Route::patch(
    'sales-returns/{salesReturn}/cancel',
    [SalesReturnController::class, 'cancel']
)->name('sales-returns.cancel');
/*
|--------------------------------------------------------------------------
| Future Reports (Reserved)
|--------------------------------------------------------------------------
|
| Keep these routes grouped together. They will be enabled when the
| Reports module is completed.
|
*/

Route::prefix('reports')
    ->name('reports.')
    ->group(function () {

        // Sales
        // Route::get('/sales', ...)->name('sales');

        // Purchases
        // Route::get('/purchases', ...)->name('purchases');

        // Inventory
        // Route::get('/inventory', ...)->name('inventory');

        // Profit & Loss
        // Route::get('/profit-loss', ...)->name('profit-loss');

        // Stock Valuation
        // Route::get('/stock-valuation', ...)->name('stock-valuation');

    });

    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    */

    Route::get('/reports/purchases', [PurchaseReportController::class, 'index'])
        ->name('reports.purchases');

    /*
    |--------------------------------------------------------------------------
    | User Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/reports.php';