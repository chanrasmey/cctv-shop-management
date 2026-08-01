<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReturnRequest;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Services\Inventory\SalesReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class SalesReportController extends Controller
{
    protected SalesReturnService $salesReturnService;

    public function __construct(
        SalesReturnService $salesReturnService
    ) {
        $this->salesReturnService = $salesReturnService;
    }

    /**
     * Display Sales Return list.
     */
    public function index(): View
    {
        $salesReturns = SalesReturn::with([
                'sale',
                'customer',
                'creator',
            ])
            ->latest()
            ->paginate(15);

        return view(
            'sales_returns.index',
            compact('salesReturns')
        );
    }

    /**
     * Show Create Sales Return page.
     */
    public function create(
        Sale $sale
    ): View {

        $sale->load([
            'customer',
            'details.product',
        ]);

        $returnNo = $this->salesReturnService
            ->generateReturnNumber();

        return view(
            'sales_returns.create',
            compact(
                'sale',
                'returnNo'
            )
        );
    }

    /**
     * Store Sales Return.
     */
    public function store(
        StoreSalesReturnRequest $request,
        Sale $sale
    ): RedirectResponse {

        try {

            $salesReturn = $this->salesReturnService->create(
                $sale,
                $request->validated()
            );

            return redirect()
                ->route(
                    'sales-returns.show',
                    $salesReturn
                )
                ->with(
                    'success',
                    'Sales Return '
                    . $salesReturn->return_no .
                    ' created successfully.'
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
     * Display Sales Return.
     */
    public function show(
        SalesReturn $salesReturn
    ): View {

        $salesReturn->load([
            'sale.customer',
            'details.product',
            'details.saleDetail',
            'creator',
        ]);

        return view(
            'sales_returns.show',
            compact('salesReturn')
        );
    }

    /**
     * Print Sales Return.
     */
    public function print(
        SalesReturn $salesReturn
    ): View {

        $salesReturn->load([
            'sale.customer',
            'details.product',
            'details.saleDetail',
            'creator',
        ]);

        return view(
            'sales_returns.print',
            compact('salesReturn')
        );
    }

    /**
     * Cancel Sales Return.
     */
    public function cancel(
        SalesReturn $salesReturn
    ): RedirectResponse {

        try {

            $salesReturn = $this->salesReturnService
                ->cancel($salesReturn);

            return redirect()
                ->route(
                    'sales-returns.show',
                    $salesReturn
                )
                ->with(
                    'success',
                    'Sales Return '
                    . $salesReturn->return_no .
                    ' cancelled successfully.'
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
     * Delete Draft Sales Return.
     */
    public function destroy(
        SalesReturn $salesReturn
    ): RedirectResponse {

        try {

            $this->salesReturnService
                ->deleteDraft($salesReturn);

            return redirect()
                ->route('sales-returns.index')
                ->with(
                    'success',
                    'Sales Return deleted successfully.'
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