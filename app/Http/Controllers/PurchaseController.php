<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\Inventory\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    /**
     * Display Purchase List
     */
    public function index(): View
    {
        $purchases = Purchase::with('supplier')
            ->latest()
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show Create Purchase Page
     */
    public function create(): View
    {
        $purchaseNo = $this->purchaseService->generatePurchaseNumber();
        $suppliers = Supplier::orderBy('company_name')->get();
        $products = Product::where('status', 1)
            ->orderBy('product_name')
            ->get();

        return view('purchases.create', compact(
            'purchaseNo',
            'suppliers',
            'products'
        ));
    }

    /**
     * Save Purchase
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        try {
            $purchase = $this->purchaseService->create(
                $request->validated()
            );

            return redirect()
                ->route('purchases.show', $purchase)
                ->with(
                    'success',
                    'Purchase '.$purchase->purchase_no.' created successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display Purchase
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load([
            'supplier',
            'details.product',
            'details.returnDetails.purchaseReturn',
            'returns.details.product',
            'creator',
        ]);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Edit Purchase
     */
    public function edit(Purchase $purchase): View|RedirectResponse
    {
        if (! $purchase->isDraft() && ! $purchase->isPending()) {
            return redirect()
                ->route('purchases.show', $purchase)
                ->with('error', 'Only Draft or Pending purchases can be edited.');
        }

        $purchase->load('details.product');
        $suppliers = Supplier::orderBy('company_name')->get();
        $products = Product::where('status', 1)
            ->orderBy('product_name')
            ->get();

        return view('purchases.edit', compact(
            'purchase',
            'suppliers',
            'products'
        ));
    }

    /**
     * Update Purchase
     */
    public function update(StorePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        try {
            $purchase = $this->purchaseService->update(
                $purchase,
                $request->validated()
            );

            return redirect()
                ->route('purchases.show', $purchase)
                ->with(
                    'success',
                    'Purchase '.$purchase->purchase_no.' updated successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel Purchase.
     */
    public function cancel(Purchase $purchase): RedirectResponse
    {
        try {
            $purchase = $this->purchaseService->cancel($purchase);

            return redirect()
                ->route('purchases.show', $purchase)
                ->with(
                    'success',
                    'Purchase '.$purchase->purchase_no.' cancelled successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    /**
     * Delete Draft Purchase.
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        try {
            $this->purchaseService->deleteDraft($purchase);

            return redirect()
                ->route('purchases.index')
                ->with(
                    'success',
                    'Purchase deleted successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }
}
