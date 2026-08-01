@extends('adminlte::page')

@section('title', 'Sales Return Details')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="mb-1">

            <i class="fas fa-undo-alt text-danger"></i>

            Sales Return Details

        </h1>

        <small class="text-muted">

            View Sales Return information

        </small>

    </div>

    <div>

        <a href="{{ route('sales-returns.print', $salesReturn) }}"
           target="_blank"
           class="btn btn-primary">

            <i class="fas fa-print mr-1"></i>

            Print

        </a>

        <a href="{{ route('sales-returns.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left mr-1"></i>

            Back

        </a>

    </div>

</div>

@stop

@section('content')

@include('partials.alert')

<div class="row">

    <div class="col-lg-12">

        <div class="card card-outline card-danger">

            <div class="card-header">

                <h3 class="card-title">

                    Sales Return Information

                </h3>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <table class="table table-sm table-borderless">

                            <tr>

                                <th width="140">Return No</th>

                                <td>{{ $salesReturn->return_no }}</td>

                            </tr>

                            <tr>

                                <th>Return Date</th>

                                <td>

                                    {{ optional($salesReturn->return_date)->format('d/m/Y') }}

                                </td>

                            </tr>

                            <tr>

                                <th>Status</th>

                                <td>

                                    @if($salesReturn->status=='Completed')

                                        <span class="badge badge-success">

                                            Completed

                                        </span>

                                    @elseif($salesReturn->status=='Draft')

                                        <span class="badge badge-warning">

                                            Draft

                                        </span>

                                    @else

                                        <span class="badge badge-secondary">

                                            Cancelled

                                        </span>

                                    @endif

                                </td>

                            </tr>

                        </table>

                    </div>

                    <div class="col-md-4">

                        <table class="table table-sm table-borderless">

                            <tr>

                                <th width="140">

                                    Sale No

                                </th>

                                <td>

                                    {{ $salesReturn->sale?->sale_no }}

                                </td>

                            </tr>

                            <tr>

                                <th>Customer</th>

                                <td>

                                    {{ $salesReturn->customer?->customer_name }}

                                </td>

                            </tr>

                            <tr>

                                <th>Created By</th>

                                <td>

                                    {{ $salesReturn->creator?->name }}

                                </td>

                            </tr>

                        </table>

                    </div>

                    <div class="col-md-4">

                        <table class="table table-sm table-borderless">

                            <tr>

                                <th width="140">

                                    Total Amount

                                </th>

                                <td>

                                    <strong>

                                        {{ number_format($salesReturn->subtotal,2) }}

                                    </strong>

                                </td>

                            </tr>

                            <tr>

                                <th>Remark</th>

                                <td>

                                    {{ $salesReturn->remark ?: '-' }}

                                </td>

                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="card card-outline card-danger">

            <div class="card-header">

                <h3 class="card-title">

                    Returned Items

                </h3>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover mb-0">

                        <thead>

                            <tr>

                                <th width="60">#</th>

                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Unit Cost</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Subtotal</th>
                                <th>Reason</th>

                            </tr>

                        </thead>

                        <tbody>

{{-- PART 2 STARTS HERE --}}
                        @forelse($salesReturn->details as $detail)

                            <tr>

                                <td>

                                    {{ $loop->iteration }}

                                </td>

                                <td>

                                    <strong>

                                        {{ $detail->product?->product_name }}

                                    </strong>

                                </td>

                                <td class="text-center">

                                    {{ number_format($detail->qty,2) }}

                                </td>

                                <td class="text-right">

                                    {{ number_format($detail->unit_cost,2) }}

                                </td>

                                <td class="text-right">

                                    {{ number_format($detail->unit_price,2) }}

                                </td>

                                <td class="text-right">

                                    <strong>

                                        {{ number_format($detail->subtotal,2) }}

                                    </strong>

                                </td>

                                <td>

                                    {{ $detail->reason ?: '-' }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center text-muted py-5">

                                    <i class="fas fa-box-open fa-3x mb-3"></i>

                                    <br>

                                    No returned items found.

                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                        <tfoot>

                            <tr>

                                <th colspan="5"
                                    class="text-right">

                                    Grand Total

                                </th>

                                <th class="text-right">

                                    {{ number_format($salesReturn->subtotal,2) }}

                                </th>

                                <th></th>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@stop

@section('css')

<style>

.table td,
.table th{
    vertical-align:middle;
}

.badge{
    font-size:.85rem;
}

.card-title{
    font-weight:600;
}

.table tfoot th{
    font-size:1rem;
}

</style>

@stop