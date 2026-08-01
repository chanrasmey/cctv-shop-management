@extends('adminlte::page')

@section('title', 'Sales Returns')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>

        <i class="fas fa-undo-alt text-warning"></i>

        Sales Returns

    </h1>

    <div>

        <a
            href="{{ route('sales.index') }}"
            class="btn btn-success">

            <i class="fas fa-plus"></i>

            New Sales Return

        </a>

    </div>

</div>

@stop

@section('content')

<div class="row mb-3">

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>

                    {{ $todayReturns ?? 0 }}

                </h3>

                <p>

                    Today's Returns

                </p>

            </div>

            <div class="icon">

                <i class="fas fa-calendar-day"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>

                    {{ $monthReturns ?? 0 }}

                </h3>

                <p>

                    This Month

                </p>

            </div>

            <div class="icon">

                <i class="fas fa-calendar-alt"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>

                    {{ $cancelledReturns ?? 0 }}

                </h3>

                <p>

                    Cancelled

                </p>

            </div>

            <div class="icon">

                <i class="fas fa-ban"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>

                    {{ number_format($returnAmount ?? 0,2) }}

                </h3>

                <p>

                    Total Returned

                </p>

            </div>

            <div class="icon">

                <i class="fas fa-dollar-sign"></i>

            </div>

        </div>

    </div>

</div>

<div class="card card-outline card-warning">

    <div class="card-header">

        <h3 class="card-title">

            Search

        </h3>

    </div>

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-2">

                    <label>Return No</label>

                    <input
                        type="text"
                        name="return_no"
                        value="{{ request('return_no') }}"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label>Invoice</label>

                    <input
                        type="text"
                        name="invoice_no"
                        value="{{ request('invoice_no') }}"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label>Customer</label>

                    <input
                        type="text"
                        name="customer"
                        value="{{ request('customer') }}"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label>Status</label>

                    <select
                        name="status"
                        class="form-control">

                        <option value="">All</option>

                        <option
                            value="Completed"
                            @selected(request('status')=='Completed')>

                            Completed

                        </option>

                        <option
                            value="Cancelled"
                            @selected(request('status')=='Cancelled')>

                            Cancelled

                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <label>Date From</label>

                    <input
                        type="date"
                        name="from"
                        value="{{ request('from') }}"
                        class="form-control">

                </div>

                <div class="col-md-2">

                    <label>Date To</label>

                    <input
                        type="date"
                        name="to"
                        value="{{ request('to') }}"
                        class="form-control">

                </div>

            </div>

            <div class="mt-3">

                <button
                    class="btn btn-warning">

                    <i class="fas fa-search"></i>

                    Search

                </button>

                <a
                    href="{{ route('sales-returns.index') }}"
                    class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </form>

    </div>

</div>

<div class="card">

    <div class="card-body">

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover">

                <thead>

                <tr>

                    <th width="5%">#</th>

                    <th>Return No</th>

                    <th>Date</th>

                    <th>Invoice</th>

                    <th>Customer</th>

                    <th class="text-center">

                        Items

                    </th>

                    <th class="text-right">

                        Total

                    </th>

                    <th class="text-center">

                        Status

                    </th>

                    <th width="180">

                        Action

                    </th>

                </tr>

                </thead>

                <tbody>
                                @forelse($salesReturns as $salesReturn)

                    <tr>

                        <td class="text-center">

                            {{ $loop->iteration + (($salesReturns->currentPage() - 1) * $salesReturns->perPage()) }}

                        </td>

                        <td>

                            <strong>

                                {{ $salesReturn->return_no }}

                            </strong>

                        </td>

                        <td>

                            {{ $salesReturn->return_date->format('d/m/Y') }}

                        </td>

                        <td>

                            {{ $salesReturn->sale->invoice_no ?? $salesReturn->sale->sale_no }}

                        </td>

                        <td>

                            {{ $salesReturn->sale->customer->name }}

                        </td>

                        <td class="text-center">

                            {{ $salesReturn->details->count() }}

                        </td>

                        <td class="text-right">

                            {{ number_format($salesReturn->grand_total,2) }}

                        </td>

                        <td class="text-center">

                            @if($salesReturn->status == 'Completed')

                                <span class="badge badge-success">

                                    Completed

                                </span>

                            @elseif($salesReturn->status == 'Cancelled')

                                <span class="badge badge-danger">

                                    Cancelled

                                </span>

                            @else

                                <span class="badge badge-warning">

                                    {{ $salesReturn->status }}

                                </span>

                            @endif

                        </td>

                        <td>

                            <a
                                href="{{ route('sales-returns.show',$salesReturn) }}"
                                class="btn btn-info btn-sm">

                                <i class="fas fa-eye"></i>

                            </a>

                            <a
                                href="{{ route('sales-returns.print',$salesReturn) }}"
                                class="btn btn-primary btn-sm"
                                target="_blank">

                                <i class="fas fa-print"></i>

                            </a>

                            @if($salesReturn->status != 'Cancelled')

                                <form
                                    action="{{ route('sales-returns.cancel',$salesReturn) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Cancel this Sales Return?')">

                                        <i class="fas fa-ban"></i>

                                    </button>

                                </form>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="text-center text-muted py-4">

                            <i class="fas fa-inbox fa-2x mb-2"></i>

                            <br>

                            No Sales Returns Found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

    @if(method_exists($salesReturns,'links'))

    <div class="card-footer clearfix">

        <div class="float-right">

            {{ $salesReturns->withQueryString()->links() }}

        </div>

    </div>

    @endif

</div>

@stop