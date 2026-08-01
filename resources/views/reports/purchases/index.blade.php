@extends('adminlte::page')

@section('title', 'Purchase Report')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="mb-1">Purchase Report</h1>
        <small class="text-muted">
            Purchase transactions summary and analysis
        </small>
    </div>

    <div>
        <a href="{{ route('reports.purchases.print', request()->query()) }}"
           target="_blank"
           class="btn btn-secondary">
            <i class="fas fa-print"></i> Print
        </a>

        <a href="{{ route('reports.purchases.pdf', request()->query()) }}"
           class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> PDF
        </a>

        <a href="{{ route('reports.purchases.excel', request()->query()) }}"
           class="btn btn-success">
            <i class="fas fa-file-excel"></i> Excel
        </a>
    </div>
</div>
@stop

@section('content')

<div class="card card-outline card-primary">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-filter mr-1"></i>
            Filter
        </h3>
    </div>

    <form method="GET">

        <div class="card-body">

            <div class="row">

                <div class="col-md-3">

                    <div class="form-group">

                        <label>Date From</label>

                        <input
                            type="date"
                            name="date_from"
                            value="{{ $filters['date_from'] ?? '' }}"
                            class="form-control">

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="form-group">

                        <label>Date To</label>

                        <input
                            type="date"
                            name="date_to"
                            value="{{ $filters['date_to'] ?? '' }}"
                            class="form-control">

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="form-group">

                        <label>Supplier</label>

                        <select
                            name="supplier_id"
                            class="form-control">

                            <option value="">
                                All Suppliers
                            </option>

                            @foreach($suppliers as $supplier)

                                <option
                                    value="{{ $supplier->id }}"
                                    @selected(($filters['supplier_id'] ?? '') == $supplier->id)>

                                    {{ $supplier->company_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="form-group">

                        <label>Status</label>

                        <select
                            name="status"
                            class="form-control">

                            <option value="">
                                All Status
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

                </div>

            </div>

        </div>

        <div class="card-footer">

            <button
                class="btn btn-primary">

                <i class="fas fa-search"></i>

                Search

            </button>

            <a
                href="{{ route('reports.purchases') }}"
                class="btn btn-secondary">

                Reset

            </a>

        </div>

    </form>

</div>

<div class="row">

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($summary['purchase_count']) }}</h3>
                <p>Total Purchases</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($summary['grand_total'],2) }}</h3>
                <p>Gross Total</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($summary['paid'],2) }}</h3>
                <p>Paid</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($summary['balance'],2) }}</h3>
                <p>Balance</p>
            </div>
            <div class="icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

</div>
<div class="row">

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ number_format($summary['subtotal'],2) }}</h3>
                <p>Subtotal</p>
            </div>
            <div class="icon">
                <i class="fas fa-calculator"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ number_format($summary['discount'],2) }}</h3>
                <p>Discount</p>
            </div>
            <div class="icon">
                <i class="fas fa-percent"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-indigo">
            <div class="inner">
                <h3>{{ number_format($summary['tax'],2) }}</h3>
                <p>Tax</p>
            </div>
            <div class="icon">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-maroon">
            <div class="inner">
                <h3>{{ number_format($summary['return_total'],2) }}</h3>
                <p>Return Total</p>
            </div>
            <div class="icon">
                <i class="fas fa-undo"></i>
            </div>
        </div>
    </div>

</div>

<div class="card card-outline card-primary">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-table mr-1"></i>
            Purchase Transactions
        </h3>
    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-hover table-striped">

            <thead>

            <tr>

                <th width="120">Date</th>

                <th width="170">Purchase No</th>

                <th>Supplier</th>

                <th class="text-center">Items</th>

                <th class="text-right">Grand Total</th>

                <th class="text-right">Paid</th>

                <th class="text-right">Balance</th>

                <th class="text-center">Status</th>

                <th width="90" class="text-center">Action</th>

            </tr>

            </thead>

            <tbody>

            @forelse($purchases as $purchase)

                <tr>

                    <td>
                        {{ optional($purchase->purchase_date)->format('Y-m-d') }}
                    </td>

                    <td>
                        <strong>{{ $purchase->purchase_no }}</strong>
                    </td>

                    <td>
                        {{ $purchase->supplier->company_name ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ $purchase->details->sum('qty') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($purchase->grand_total,2) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($purchase->paid_amount,2) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($purchase->balance,2) }}
                    </td>

                    <td class="text-center">

                        @php
                            $badge = match($purchase->status){
                                'Completed' => 'success',
                                'Pending' => 'warning',
                                'Cancelled' => 'danger',
                                default => 'secondary',
                            };
                        @endphp

                        <span class="badge badge-{{ $badge }}">
                            {{ $purchase->status }}
                        </span>

                    </td>

                    <td class="text-center">

                        <a href="{{ route('purchases.show',$purchase) }}"
                           class="btn btn-xs btn-info">

                            <i class="fas fa-eye"></i>

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9" class="text-center text-muted py-5">

                        <i class="fas fa-folder-open fa-2x mb-2"></i>

                        <br>

                        No purchase records found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>
</div>
<div class="row mt-3">

    <div class="col-md-6">

        <div class="small-box bg-teal">

            <div class="inner">

                <h3>{{ number_format($summary['net_total'],2) }}</h3>

                <p>Net Purchase</p>

            </div>

            <div class="icon">

                <i class="fas fa-chart-line"></i>

            </div>

        </div>

    </div>

</div>

<div class="d-flex justify-content-between align-items-center mt-3">

    <div class="text-muted">

        Showing

        <strong>{{ $purchases->firstItem() ?? 0 }}</strong>

        -

        <strong>{{ $purchases->lastItem() ?? 0 }}</strong>

        of

        <strong>{{ $purchases->total() }}</strong>

        purchase records.

    </div>

    <div>

        {{ $purchases->links() }}

    </div>

</div>

@stop

@push('css')
<style>

.small-box h3{
    font-size:1.6rem;
    font-weight:700;
}

.table td,
.table th{
    vertical-align:middle;
}

.badge{
    font-size:.85rem;
}

</style>
@endpush

@push('js')
<script>

document.addEventListener('DOMContentLoaded', function () {

    const form = document.querySelector('form');

    if(form){

        form.addEventListener('submit', function(){

            const btn = this.querySelector('button[type=submit]');

            if(btn){

                btn.disabled = true;

                btn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Loading...';

            }

        });

    }

});

</script>
@endpush