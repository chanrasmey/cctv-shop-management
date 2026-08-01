<?php

use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StockReportController;
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

        Route::get('/inventory', [PurchaseReportController::class, 'inventory'])
            ->name('inventory');

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
    });