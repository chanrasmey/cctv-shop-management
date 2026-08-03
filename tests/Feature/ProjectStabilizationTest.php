<?php

use App\Exports\BalanceSheetReportExport;
use App\Exports\ProfitLossReportExport;
use App\Exports\StockCardReportExport;
use App\Exports\StockValuationReportExport;
use App\Http\Controllers\SalesReportController;
use App\Models\SaleDetail;
use App\Services\Purchases\PurchaseReturnService;
use App\Services\Purchases\PurchaseService;
use App\Services\Reports\ProfitLossReportService;
use App\Services\Reports\StockCardReportService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

test('the stabilized services and exports can be loaded', function () {
    expect(class_exists(ProfitLossReportService::class))->toBeTrue()
        ->and(class_exists(StockCardReportService::class))->toBeTrue()
        ->and(class_exists(SalesReportController::class))->toBeTrue()
        ->and(class_exists(BalanceSheetReportExport::class))->toBeTrue()
        ->and(class_exists(ProfitLossReportExport::class))->toBeTrue()
        ->and(class_exists(StockCardReportExport::class))->toBeTrue()
        ->and(class_exists(StockValuationReportExport::class))->toBeTrue();
});

test('purchase services resolve their inventory dependencies', function () {
    expect(app(PurchaseService::class))->toBeInstanceOf(PurchaseService::class)
        ->and(app(PurchaseReturnService::class))->toBeInstanceOf(PurchaseReturnService::class);
});

test('purchase return number generation is available to its controller', function () {
    expect((new ReflectionMethod(PurchaseReturnService::class, 'generateReturnNumber'))->isPublic())
        ->toBeTrue();
});

test('sale details expose their sales return details', function () {
    expect((new SaleDetail())->salesReturns())->toBeInstanceOf(HasMany::class);
});

test('stabilized operation and report routes point to existing controller actions', function () {
    foreach ([
        'purchase-returns.index',
        'purchase-returns.cancel',
        'purchase-returns.print',
        'purchases.details',
        'reports.inventory.index',
        'reports.sales',
        'reports.sales.print',
        'reports.sales.excel',
        'reports.sales.pdf',
        'reports.stock-card.index',
        'reports.stock-valuation.index',
        'reports.profit-loss.index',
        'reports.balance-sheet.index',
    ] as $name) {
        expect(Route::has($name))->toBeTrue();
    }
});
