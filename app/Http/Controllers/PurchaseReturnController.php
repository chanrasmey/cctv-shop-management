<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseReturnRequest;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Services\Inventory\PurchaseReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class PurchaseReturnController extends Controller
{
    public function __construct(
        protected PurchaseReturnService $purchaseReturnService
    ) {}

    public function create(Purchase $purchase): View|RedirectResponse
    {
        if (! $purchase->isCompleted()) {
            return redirect()
                ->route('purchases.show', $purchase)
                ->with('error', 'Only Completed purchases can be returned.');
        }

        $purchase->load([
            'supplier',
            'details.product',
            'details.returnDetails.purchaseReturn',
        ]);

        return view('purchase_returns.create', compact('purchase'));
    }

    public function store(StorePurchaseReturnRequest $request, Purchase $purchase): RedirectResponse
    {
        try {
            $purchaseReturn = $this->purchaseReturnService->create(
                $purchase,
                $request->validated()
            );

            return redirect()
                ->route('purchase-returns.show', $purchaseReturn)
                ->with(
                    'success',
                    'Purchase return '.$purchaseReturn->return_no.' created successfully.'
                );
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(PurchaseReturn $purchaseReturn): View
    {
        $purchaseReturn->load([
            'purchase',
            'supplier',
            'details.product',
            'details.purchaseDetail',
            'creator',
        ]);

        return view('purchase_returns.show', compact('purchaseReturn'));
    }
}
