@extends('adminlte::page')

@section('title', 'Sales Report')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">

    <h1 class="mb-0">
        <i class="fas fa-chart-line text-success mr-2"></i>
        Sales Report
    </h1>

    <div>

        <a href="{{ route('reports.sales.print', request()->query()) }}"
           target="_blank"
           class="btn btn-success">

            <i class="fas fa-print"></i>
            Print

        </a>

        <a href="{{ route('reports.sales.excel', request()->query()) }}"
           class="btn btn-info">

            <i class="fas fa-file-excel"></i>
            Excel

        </a>

        <a href="{{ route('reports.sales.pdf', request()->query()) }}"
           target="_blank"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i>
            PDF

        </a>

    </div>

</div>
@stop

@section('content')

<div class="card card-outline card-success">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-filter mr-1"></i>

            Filter

        </h3>

    </div>

    <form method="GET"
          action="{{ route('reports.sales') }}">

        <div class="card-body">

            <div class="row">

                <div class="col-md-2">

                    <label>Date From</label>

                    <input
                        type="date"
                        name="date_from"
                        class="form-control"
                        value="{{ $filters['date_from'] ?? '' }}">

                </div>

                <div class="col-md-2">

                    <label>Date To</label>

                    <input
                        type="date"
                        name="date_to"
                        class="form-control"
                        value="{{ $filters['date_to'] ?? '' }}">

                </div>

                <div class="col-md-3">

                    <label>Customer</label>

                    <select
                        name="customer_id"
                        class="form-control">

                        <option value="">
                            All Customers
                        </option>

                        @foreach($customers as $customer)

                            <option
                                value="{{ $customer->id }}"
                                @selected(($filters['customer_id'] ?? '') == $customer->id)>

                                {{ $customer->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-2">

                    <label>Status</label>

                    <select
                        name="status"
                        class="form-control">

                        <option value="">
                            All
                        </option>

                        @foreach($statuses as $status)

                            <option
                                value="{{ $status }}"
                                @selected(($filters['status'] ?? '') == $status)>

                                {{ $status }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-3 d-flex align-items-end">

                    <button
                        class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>

                        Search

                    </button>

                    <a
                        href="{{ route('reports.sales') }}"
                        class="btn btn-secondary">

                        <i class="fas fa-sync-alt"></i>

                        Reset

                    </a>

                </div>

            </div>

        </div>

    </form>

</div>

<div class="row">

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ number_format($summary['sales'],2) }}</h3>

                <p>Total Sales</p>

            </div>

            <div class="icon">

                <i class="fas fa-dollar-sign"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>{{ number_format($summary['profit'],2) }}</h3>

                <p>Gross Profit</p>

            </div>

            <div class="icon">

                <i class="fas fa-chart-line"></i>

            </div>

        </div>

    </div>
    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ number_format($summary['paid'],2) }}</h3>

                <p>Total Paid</p>

            </div>

            <div class="icon">

                <i class="fas fa-money-check-alt"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>{{ number_format($summary['balance'],2) }}</h3>

                <p>Outstanding</p>

            </div>

            <div class="icon">

                <i class="fas fa-wallet"></i>

            </div>

        </div>

    </div>

</div>

<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-list mr-1"></i>

            Sales List

        </h3>

        <div class="card-tools">

            <span class="badge badge-primary">

                {{ $sales->total() }} Record(s)

            </span>

        </div>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-hover table-striped">

            <thead>

            <tr>

                <th width="110">Date</th>

                <th width="160">Invoice</th>

                <th>Customer</th>

                <th class="text-center" width="80">Items</th>

                <th class="text-right" width="130">Grand Total</th>

                <th class="text-right" width="120">Paid</th>

                <th class="text-right" width="120">Balance</th>

                <th class="text-center" width="120">Status</th>

            </tr>

            </thead>

            <tbody>

            @forelse($sales as $sale)

                <tr>

                    <td>

                        {{ $sale->sale_date->format('Y-m-d') }}

                    </td>

                    <td>

                        <strong>{{ $sale->invoice_no }}</strong>

                    </td>

                    <td>

                        {{ optional($sale->customer)->name }}

                    </td>

                    <td class="text-center">

                        {{ $sale->details->count() }}

                    </td>

                    <td class="text-right">

                        {{ number_format($sale->grand_total,2) }}

                    </td>

                    <td class="text-right">

                        {{ number_format($sale->paid_amount,2) }}

                    </td>

                    <td class="text-right">

                        {{ number_format($sale->balance,2) }}

                    </td>

                    <td class="text-center">

                        @switch($sale->status)

                            @case('Completed')

                                <span class="badge badge-success">

                                    Completed

                                </span>

                                @break

                            @case('Pending')

                                <span class="badge badge-warning">

                                    Pending

                                </span>

                                @break

                            @case('Cancelled')

                                <span class="badge badge-danger">

                                    Cancelled

                                </span>

                                @break

                            @default

                                <span class="badge badge-secondary">

                                    {{ $sale->status }}

                                </span>

                        @endswitch

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8" class="text-center text-muted py-4">

                        <i class="fas fa-folder-open fa-2x mb-2"></i>

                        <br>

                        No sales found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    @if($sales->hasPages())

        <div class="card-footer clearfix">

            {{ $sales->withQueryString()->links() }}

        </div>

    @endif

</div>

@stop