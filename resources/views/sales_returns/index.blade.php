@extends('adminlte::page')

@section('title', 'Sales Return Report')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">

    <h1 class="mb-0">
        <i class="fas fa-undo text-danger mr-2"></i>
        Sales Return Report
    </h1>

    <div>

        <a href="{{ route('reports.sales_returns.print', request()->query()) }}"
           target="_blank"
           class="btn btn-success">

            <i class="fas fa-print"></i>
            Print

        </a>

        <a href="{{ route('reports.sales_returns.excel', request()->query()) }}"
           class="btn btn-info">

            <i class="fas fa-file-excel"></i>
            Excel

        </a>

        <a href="{{ route('reports.sales_returns.pdf', request()->query()) }}"
           target="_blank"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i>
            PDF

        </a>

    </div>

</div>
@stop

@section('content')

<div class="card card-outline card-danger">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-filter mr-1"></i>

            Filter

        </h3>

    </div>

    <form method="GET"
          action="{{ route('reports.sales_returns') }}">

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

                    <button class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>

                        Search

                    </button>

                    <a href="{{ route('reports.sales_returns') }}"
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

    <div class="col-lg-4">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>{{ number_format($summary['returns'],2) }}</h3>

                <p>Total Return</p>

            </div>

            <div class="icon">

                <i class="fas fa-undo"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ number_format($summary['refund'],2) }}</h3>

                <p>Total Refund</p>

            </div>

            <div class="icon">

                <i class="fas fa-money-bill-wave"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ number_format($summary['items']) }}</h3>

                <p>Returned Qty</p>

            </div>

            <div class="icon">

                <i class="fas fa-box-open"></i>

            </div>

        </div>

    </div>

</div>

<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">

            Sales Return List

        </h3>

        <div class="card-tools">

            <span class="badge badge-primary">

                {{ $returns->total() }} Record(s)

            </span>

        </div>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-hover table-striped">

            <thead>

            <tr>

                <th>Date</th>
                <th>Return No</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th class="text-center">Items</th>
                <th class="text-right">Total</th>
                <th class="text-center">Status</th>

            </tr>

            </thead>

            <tbody>

            @forelse($returns as $return)

                <tr>

                    <td>{{ $return->return_date->format('Y-m-d') }}</td>

                    <td>{{ $return->return_no }}</td>

                    <td>{{ optional($return->sale)->invoice_no }}</td>

                    <td>{{ optional($return->customer)->name }}</td>

                    <td class="text-center">

                        {{ $return->details->count() }}

                    </td>

                    <td class="text-right">

                        {{ number_format($return->grand_total,2) }}

                    </td>

                    <td class="text-center">

                        <span class="badge badge-{{ $return->status == 'Completed' ? 'success' : ($return->status == 'Cancelled' ? 'danger' : 'secondary') }}">

                            {{ $return->status }}

                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7"
                        class="text-center text-muted py-4">

                        No sales returns found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    @if($returns->hasPages())

        <div class="card-footer">

            {{ $returns->withQueryString()->links() }}

        </div>

    @endif

</div>

@stop