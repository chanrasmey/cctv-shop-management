@extends('adminlte::page')

@section('title', 'Purchase Return Report')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">

    <div>
        <h1 class="mb-1">Purchase Return Report</h1>
        <small class="text-muted">
            Purchase return transactions summary and analysis
        </small>
    </div>

    <div>

        <a href="{{ route('reports.purchase_returns.print', request()->query()) }}"
           target="_blank"
           class="btn btn-secondary">
            <i class="fas fa-print"></i> Print
        </a>

        <a href="{{ route('reports.purchase_returns.pdf', request()->query()) }}"
           class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> PDF
        </a>

        <a href="{{ route('reports.purchase_returns.excel', request()->query()) }}"
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
                href="{{ route('reports.purchase_returns') }}"
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

                <h3>{{ number_format($summary['returns']) }}</h3>

                <p>Total Returns</p>

            </div>

            <div class="icon">

                <i class="fas fa-undo"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>{{ number_format($summary['items']) }}</h3>

                <p>Total Items</p>

            </div>

            <div class="icon">

                <i class="fas fa-boxes"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ number_format($summary['grand_total'],2) }}</h3>

                <p>Grand Total</p>

            </div>

            <div class="icon">

                <i class="fas fa-dollar-sign"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-danger">

            <div class="inner">

                <h3>{{ number_format($summary['refund'],2) }}</h3>

                <p>Refund Amount</p>

            </div>

            <div class="icon">

                <i class="fas fa-money-bill-wave"></i>

            </div>

        </div>

    </div>

</div>
<div class="row">

    <div class="col-lg-4 col-md-6">

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

    <div class="col-lg-4 col-md-6">

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

    <div class="col-lg-4 col-md-6">

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

</div>

<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-table mr-1"></i>

            Purchase Return Transactions

        </h3>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-hover table-striped">

            <thead>

            <tr>

                <th width="120">Return Date</th>

                <th width="170">Return No</th>

                <th width="170">Purchase No</th>

                <th>Supplier</th>

                <th class="text-center">Qty</th>

                <th class="text-right">Grand Total</th>

                <th class="text-right">Refund</th>

                <th class="text-center">Status</th>

                <th width="90" class="text-center">Action</th>

            </tr>

            </thead>

            <tbody>

            @forelse($returns as $return)

                <tr>

                    <td>
                        {{ optional($return->return_date)->format('Y-m-d') }}
                    </td>

                    <td>
                        <strong>{{ $return->return_no }}</strong>
                    </td>

                    <td>
                        {{ $return->purchase->purchase_no ?? '-' }}
                    </td>

                    <td>
                        {{ $return->supplier->company_name ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ $return->details->sum('qty') }}
                    </td>

                    <td class="text-right">
                        {{ number_format($return->grand_total,2) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($return->refund_amount,2) }}
                    </td>

                    <td class="text-center">

                        @php
                            $badge = match($return->status){
                                'Completed' => 'success',
                                'Pending'   => 'warning',
                                'Cancelled' => 'danger',
                                default     => 'secondary',
                            };
                        @endphp

                        <span class="badge badge-{{ $badge }}">
                            {{ $return->status }}
                        </span>

                    </td>

                    <td class="text-center">

                        <a href="{{ route('purchase-returns.show', $return) }}"
                           class="btn btn-xs btn-info">

                            <i class="fas fa-eye"></i>

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9"
                        class="text-center text-muted py-5">

                        <i class="fas fa-folder-open fa-2x mb-2"></i>

                        <br>

                        No purchase return records found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>
<div class="d-flex justify-content-between align-items-center mt-3">

    <div class="text-muted">

        Showing

        <strong>{{ $returns->firstItem() ?? 0 }}</strong>

        -

        <strong>{{ $returns->lastItem() ?? 0 }}</strong>

        of

        <strong>{{ $returns->total() }}</strong>

        purchase return records.

    </div>

    <div>

        {{ $returns->links() }}

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

.table td.text-right,
.table th.text-right{
    text-align:right;
}

.table td.text-center,
.table th.text-center{
    text-align:center;
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