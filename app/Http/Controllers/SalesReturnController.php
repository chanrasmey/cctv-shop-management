<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesReturnRequest;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Services\Sales\SalesReturnService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SalesReturnController extends Controller
{
    public function __construct(
        protected SalesReturnService $salesReturnService
    ) {
    }

    /**
     * Display a listing of Sales Returns.
     */
    public function index(Request $request): View
    {
        $query = SalesReturn::query()
            ->with([
                'customer',
                'sale',
            ]);

        if ($keyword = trim((string) $request->input('keyword'))) {

            $query->where(function ($q) use ($keyword) {

                $q->where('return_no', 'like', "%{$keyword}%")
                    ->orWhereHas('customer', function ($customer) use ($keyword) {

                        $customer->where('customer_name', 'like', "%{$keyword}%");

                    });

            });
        }

        if ($request->filled('customer_id')) {

            $query->where('customer_id', $request->customer_id);

        }

        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }

        if ($request->filled('date_from')) {

            $query->whereDate('return_date', '>=', $request->date_from);

        }

        if ($request->filled('date_to')) {

            $query->whereDate('return_date', '<=', $request->date_to);

        }

        $salesReturns = $query
            ->latest('return_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('sales_returns.index', [
            'salesReturns' => $salesReturns,
            'customers'    => Customer::orderBy('customer_name')->get(),
            'statuses'     => [
                'Draft',
                'Completed',
                'Cancelled',
            ],
        ]);
    }
    /**
     * Show the form for creating a new Sales Return.
     */
    public function create(): View
    {
        $sales = Sale::query()
            ->with([
                'customer',
                'details.product',
                'details.salesReturns.salesReturn',
            ])
            ->where('status', 'Completed')
            ->latest('sale_date')
            ->latest('id')
            ->get();

        $sales->each(function (Sale $sale) {

            $sale->details->each(function ($detail) {

                $returnedQty = $detail->salesReturns
                    ->filter(function ($returnDetail) {

                        return optional($returnDetail->salesReturn)->status === 'Completed';

                    })
                    ->sum('qty');

                $returnedQty = (float) $returnedQty;

                $detail->returned_qty = round(
                    $returnedQty,
                    2
                );

                $detail->remaining_qty = round(
                    max(
                        0,
                        (float) $detail->qty - $returnedQty
                    ),
                    2
                );

            });

        });

        return view('sales_returns.create', [
            'sales' => $sales,
        ]);
    }
    /**
     * Store a newly created Sales Return.
     */
    public function store(
        StoreSalesReturnRequest $request
    ): RedirectResponse
    {
        try {

            $salesReturn = $this->salesReturnService->store(
                $request->validated()
            );

            return redirect()
                ->route('sales-returns.show', $salesReturn)
                ->with(
                    'success',
                    'Sales Return created successfully.'
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
     * Display the specified Sales Return.
     */
    public function show(
        SalesReturn $salesReturn
    ): View
    {
        $salesReturn->load([
            'customer',
            'sale',
            'details.product',
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
    ): View
    {
        $salesReturn->load([
            'customer',
            'sale',
            'details.product',
        ]);

        return view(
            'sales_returns.print',
            compact('salesReturn')
        );
    }
    /**
     * Cancel a Sales Return.
     */
    public function cancel(
        SalesReturn $salesReturn
    ): RedirectResponse
    {
        try {

            $this->salesReturnService->cancel($salesReturn);

            return redirect()
                ->route('sales-returns.index')
                ->with(
                    'success',
                    'Sales Return cancelled successfully.'
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
     * Remove the specified Sales Return.
     */
    public function destroy(
        SalesReturn $salesReturn
    ): RedirectResponse
    {
        try {

            $this->salesReturnService->delete($salesReturn);

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
