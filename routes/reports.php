<?php

use App\Http\Controllers\BalanceSheetReportController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\ProfitLossReportController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\PurchaseReturnReportController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SalesReturnReportController;
use App\Http\Controllers\StockCardReportController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\StockValuationReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('reports')
    ->name('reports.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Inventory Report
        |--------------------------------------------------------------------------
        */

        Route::get('/inventory', [InventoryReportController::class, 'index'])
            ->name('inventory.index');

        Route::get('/inventory/print', [InventoryReportController::class, 'print'])
            ->name('inventory.print');

        Route::get('/inventory/pdf', [InventoryReportController::class, 'pdf'])
            ->name('inventory.pdf');

        Route::get('/inventory/excel', [InventoryReportController::class, 'excel'])
            ->name('inventory.excel');

        /*
        |--------------------------------------------------------------------------
        | Purchase Report
        |--------------------------------------------------------------------------
        */

        Route::get('/purchases', [PurchaseReportController::class, 'index'])
            ->name('purchases');

        Route::get('/purchases/print', [PurchaseReportController::class, 'print'])
            ->name('purchases.print');

        Route::get('/purchases/pdf', [PurchaseReportController::class, 'pdf'])
            ->name('purchases.pdf');

        Route::get('/purchases/excel', [PurchaseReportController::class, 'excel'])
            ->name('purchases.excel');

        /*
        |--------------------------------------------------------------------------
        | Sales Report
        |--------------------------------------------------------------------------
        */

        Route::get('/sales', [SalesReportController::class, 'index'])
            ->name('sales');

        Route::get('/sales/print', [SalesReportController::class, 'print'])
            ->name('sales.print');

        Route::get('/sales/excel', [SalesReportController::class, 'excel'])
            ->name('sales.excel');

        Route::get('/sales/pdf', [SalesReportController::class, 'pdf'])
            ->name('sales.pdf');

        /*
        |--------------------------------------------------------------------------
        | Stock Report
        |--------------------------------------------------------------------------
        */

        Route::get('/stock', [StockReportController::class, 'index'])
            ->name('stock');

        Route::get('/stock/print', [StockReportController::class, 'print'])
            ->name('stock.print');

        Route::get('/stock/excel', [StockReportController::class, 'excel'])
            ->name('stock.excel');

        Route::get('/stock/pdf', [StockReportController::class, 'pdf'])
            ->name('stock.pdf');

        Route::get('/purchase-returns', [PurchaseReturnReportController::class, 'index'])
            ->name('purchase_returns');

        Route::get('/purchase-returns/print', [PurchaseReturnReportController::class, 'print'])
            ->name('purchase_returns.print');

        Route::get('/purchase-returns/pdf', [PurchaseReturnReportController::class, 'pdf'])
            ->name('purchase_returns.pdf');

        Route::get('/purchase-returns/excel', [PurchaseReturnReportController::class, 'excel'])
            ->name('purchase_returns.excel');

        Route::get('/sales-returns', [SalesReturnReportController::class, 'index'])
            ->name('sales_returns');

        Route::get('/sales-returns/print', [SalesReturnReportController::class, 'print'])
            ->name('sales_returns.print');

        Route::get('/sales-returns/pdf', [SalesReturnReportController::class, 'pdf'])
            ->name('sales_returns.pdf');

        Route::get('/sales-returns/excel', [SalesReturnReportController::class, 'excel'])
            ->name('sales_returns.excel');

        Route::get('/stock-card', [StockCardReportController::class, 'index'])
            ->name('stock-card.index');

        Route::get('/stock-card/print', [StockCardReportController::class, 'print'])
            ->name('stock-card.print');

        Route::get('/stock-card/pdf', [StockCardReportController::class, 'pdf'])
            ->name('stock-card.pdf');

        Route::get('/stock-card/excel', [StockCardReportController::class, 'excel'])
            ->name('stock-card.excel');

        Route::get('/stock-valuation', [StockValuationReportController::class, 'index'])
            ->name('stock-valuation.index');

        Route::get('/stock-valuation/print', [StockValuationReportController::class, 'print'])
            ->name('stock-valuation.print');

        Route::get('/stock-valuation/pdf', [StockValuationReportController::class, 'pdf'])
            ->name('stock-valuation.pdf');

        Route::get('/stock-valuation/excel', [StockValuationReportController::class, 'excel'])
            ->name('stock-valuation.excel');

        Route::get('/profit-loss', [ProfitLossReportController::class, 'index'])
            ->name('profit-loss.index');

        Route::get('/profit-loss/print', [ProfitLossReportController::class, 'print'])
            ->name('profit-loss.print');

        Route::get('/profit-loss/pdf', [ProfitLossReportController::class, 'pdf'])
            ->name('profit-loss.pdf');

        Route::get('/profit-loss/excel', [ProfitLossReportController::class, 'excel'])
            ->name('profit-loss.excel');

        Route::get('/balance-sheet', [BalanceSheetReportController::class, 'index'])
            ->name('balance-sheet.index');

        Route::get('/balance-sheet/print', [BalanceSheetReportController::class, 'print'])
            ->name('balance-sheet.print');

        Route::get('/balance-sheet/pdf', [BalanceSheetReportController::class, 'pdf'])
            ->name('balance-sheet.pdf');

        Route::get('/balance-sheet/excel', [BalanceSheetReportController::class, 'excel'])
            ->name('balance-sheet.excel');
    });
