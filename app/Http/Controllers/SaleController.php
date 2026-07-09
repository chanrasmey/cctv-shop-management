<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\Inventory\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    public function index(): View
    {
        $sales = Sale::with('customer')
            ->latest()
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $saleNo = $this->saleService->generateSaleNumber();
        $customers = Customer::where('status', 1)
            ->orderBy('name')
            ->get();
        $products = Product::where('status', 1)
            ->orderBy('product_name')
            ->get();

        return view('sales.create', compact(
            'saleNo',
            'customers',
            'products'
        ));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->saleService->create(
                $request->validated()
            );

            return redirect()
                ->route('sales.show', $sale)
                ->with(
                    'success',
                    'Sale '.$sale->sale_no.' created successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Sale $sale): View
    {
        $sale->load([
            'customer',
            'details.product',
            'creator',
        ]);

        return view('sales.show', compact('sale'));
    }
}
