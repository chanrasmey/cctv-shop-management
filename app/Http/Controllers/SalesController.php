<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Sales\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {
    }

    /**
 * Display sales listing.
 */
public function index(): View
{
    $sales = Sale::with([
            'customer',
            'creator',
        ])
        ->latestFirst()
        ->paginate(15);

    $todaySales = Sale::whereDate('sale_date', today())->count();

    $todayRevenue = Sale::whereDate('sale_date', today())
        ->sum('grand_total');

    $monthSales = Sale::whereYear('sale_date', now()->year)
        ->whereMonth('sale_date', now()->month)
        ->count();

    $monthRevenue = Sale::whereYear('sale_date', now()->year)
        ->whereMonth('sale_date', now()->month)
        ->sum('grand_total');

    $totalSales = Sale::count();

    $totalRevenue = Sale::sum('grand_total');

    return view('sales.index', compact(
        'sales',
        'todaySales',
        'todayRevenue',
        'monthSales',
        'monthRevenue',
        'totalSales',
        'totalRevenue'
    ));
}


    /**
 * Show create form.
 */
public function create(): View
{
    $customers = Customer::orderBy('name')->get();

    $products = Product::with('unit')
        ->active()
        ->inStock()
        ->orderBy('product_name')
        ->get();

    return view(
        'sales.create',
        compact(
            'customers',
            'products'
        )
    );
}
   /**
 * Store a newly created sale.
 */
public function store(
    StoreSaleRequest $request
): RedirectResponse
{
    try {

        $sale = $this->saleService->store(
            $request->validated()
        );

        return redirect()
            ->route('sales.show', $sale)
            ->with(
                'success',
                'Sale created successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                $e->getMessage()
            );

    }
}

  /**
 * Display sale.
 */
public function show(
    Sale $sale
): View {

    $sale->load([

        'customer',

        'creator',

        'details.product.unit',

    ]);

    return view(
        'sales.show',
        compact('sale')
    );

}
/**
 * Show edit form.
 */
public function edit(
    Sale $sale
): View {

    $sale->load([
        'details.product'
    ]);

    $customers = Customer::orderBy('name')->get();

    $products = Product::with('unit')
        ->active()
        ->orderBy('product_name')
        ->get();

    return view(
        'sales.edit',
        compact(
            'sale',
            'customers',
            'products'
        )
    );

}
   /**
 * Update sale.
 */
public function update(
    UpdateSaleRequest $request,
    Sale $sale
): RedirectResponse
{
    try {

        $this->saleService->update(
            $sale,
            $request->validated()
        );

        return redirect()
            ->route('sales.show', $sale)
            ->with(
                'success',
                'Sale updated successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                $e->getMessage()
            );

    }
}

   /**
 * Print invoice.
 */
public function print(
    Sale $sale
): View
{
    $sale->load([

        'customer',

        'creator',

        'details.product.unit',

    ]);

    return view(
        'sales.print',
        compact('sale')
    );
}
    /**
     * Duplicate an existing sale.
     */
    public function duplicate(
        Sale $sale
    ): RedirectResponse {

        return redirect()
            ->route('sales.create')
            ->with(
                'info',
                'Sale duplication will be implemented in a future version.'
            );
    }
    /**
 * Delete sale.
 */
public function destroy(
    Sale $sale
): RedirectResponse
{
    try {

        $this->saleService->delete($sale);

        return redirect()
            ->route('sales.index')
            ->with(
                'success',
                'Sale deleted successfully.'
            );

    } catch (\Throwable $e) {

        report($e);

        return back()->with(
            'error',
            $e->getMessage()
        );

    }
}
}
        