<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseReturnRequest;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Services\Purchases\PurchaseReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class PurchaseReturnController extends Controller
{
    protected PurchaseReturnService $purchaseReturnService;

    public function __construct(
        PurchaseReturnService $purchaseReturnService
    ) {
        $this->purchaseReturnService = $purchaseReturnService;
    }

    /**
     * Display Purchase Return List.
     */
    public function index(): View
    {
        $purchaseReturns = PurchaseReturn::with([
                'supplier',
                'purchase',
                'user',
            ])
            ->latest()
            ->paginate(15);

        return view(
            'purchase-returns.index',
            compact('purchaseReturns')
        );
    }

    /**
     * Create Purchase Return.
     */
    public function create(
        Purchase $purchase
    ): View {

        $purchase->load([
            'supplier',
            'details.product',
        ]);

        return view(
            'purchase-returns.create',
            [
                'purchase' => $purchase,
                'returnNo' => $this->purchaseReturnService
                    ->generateReturnNumber(),
            ]
        );
    }

    /**
     * Store Purchase Return.
     */
    public function store(
        StorePurchaseReturnRequest $request,
        Purchase $purchase
    ): RedirectResponse {

        try {

            $purchaseReturn = $this->purchaseReturnService
                ->create($purchase, array_merge(
                    $request->validated(),
                    [
                        'purchase_id' => $purchase->id,
                        'supplier_id' => $purchase->supplier_id,
                    ]
                ));

            return redirect()
                ->route(
                    'purchase-returns.show',
                    $purchaseReturn
                )
                ->with(
                    'success',
                    'Purchase Return '.$purchaseReturn->return_no.' created successfully.'
                );

        } catch (Throwable $e) {

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
     * Display Purchase Return.
     */
    public function show(
        PurchaseReturn $purchaseReturn
    ): View {

        $purchaseReturn->load([
            'supplier',
            'purchase',
            'details.product',
            'user',
        ]);

        return view(
            'purchase-returns.show',
            compact('purchaseReturn')
        );
    }
    /**
     * Cancel Purchase Return.
     */
    public function cancel(
        PurchaseReturn $purchaseReturn
    ): RedirectResponse {

        try {

            $purchaseReturn = $this->purchaseReturnService
                ->cancel($purchaseReturn);

            return redirect()
                ->route(
                    'purchase-returns.show',
                    $purchaseReturn
                )
                ->with(
                    'success',
                    'Purchase Return '.$purchaseReturn->return_no.' cancelled successfully.'
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
     * Print Purchase Return.
     */
    public function print(
        PurchaseReturn $purchaseReturn
    ): View {

        $purchaseReturn->load([
            'supplier',
            'purchase',
            'details.product',
            'user',
        ]);

        return view(
            'purchase-returns.print',
            compact('purchaseReturn')
        );
    }

    /**
     * Purchase details for AJAX.
     */
    public function purchaseDetails(
        Purchase $purchase
    )
    {
        $purchase->load([
            'supplier',
            'details.product.unit',
        ]);

        return response()->json(
            $purchase
        );
    }
}
