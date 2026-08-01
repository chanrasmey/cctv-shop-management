@extends('adminlte::page')

@section('title', 'Sales Return Details')

@section('content_header')

<div class="d-flex justify-content-between">

    <h1>

        <i class="fas fa-undo-alt text-warning"></i>

        Sales Return Details

    </h1>

    <div>

        <a
            href="{{ route('sales-returns.index') }}"
            class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

        <a
            href="{{ route('sales-returns.print', $salesReturn) }}"
            class="btn btn-primary"
            target="_blank">

            <i class="fas fa-print"></i>

            Print

        </a>

        @if($salesReturn->status != 'Cancelled')

            <form
                action="{{ route('sales-returns.cancel',$salesReturn) }}"
                method="POST"
                class="d-inline">

                @csrf
                @method('PATCH')

                <button
                    class="btn btn-danger"
                    onclick="return confirm('Cancel this Sales Return?')">

                    <i class="fas fa-ban"></i>

                    Cancel Return

                </button>

            </form>

        @endif

    </div>

</div>

@stop

@section('content')

<div class="row">

<div class="col-md-6">

<div class="card card-warning">

<div class="card-header">

<h3 class="card-title">

Return Information

</h3>

</div>

<div class="card-body">

<table class="table table-sm">

<tr>

<th width="35%">Return No</th>

<td>{{ $salesReturn->return_no }}</td>

</tr>

<tr>

<th>Return Date</th>

<td>

{{ $salesReturn->return_date->format('d/m/Y') }}

</td>

</tr>

<tr>

<th>Original Sale</th>

<td>

{{ $salesReturn->sale->invoice_no ?? $salesReturn->sale->sale_no }}

</td>

</tr>

<tr>

<th>Status</th>

<td>

@if($salesReturn->status=='Completed')

<span class="badge badge-success">

Completed

</span>

@elseif($salesReturn->status=='Cancelled')

<span class="badge badge-danger">

Cancelled

</span>

@else

<span class="badge badge-warning">

{{ $salesReturn->status }}

</span>

@endif

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

<div class="col-md-6">

<div class="card card-primary">

<div class="card-header">

<h3 class="card-title">

Customer Information

</h3>

</div>

<div class="card-body">

<table class="table table-sm">

<tr>

<th width="35%">Customer</th>

<td>

{{ $salesReturn->sale->customer->name }}

</td>

</tr>

<tr>

<th>Phone</th>

<td>

{{ $salesReturn->sale->customer->phone ?? '-' }}

</td>

</tr>

<tr>

<th>Email</th>

<td>

{{ $salesReturn->sale->customer->email ?? '-' }}

</td>

</tr>

<tr>

<th>Address</th>

<td>

{{ $salesReturn->sale->customer->address ?? '-' }}

</td>

</tr>

</table>

</div>

</div>

</div>

</div>
<div class="card card-outline card-success">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-box-open mr-1"></i>

            Returned Items

        </h3>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered table-hover">

                <thead class="thead-light">

                <tr>

                    <th width="5%">#</th>
                    <th>Product</th>
                    <th width="10%">Qty</th>
                    <th width="12%">Unit Price</th>
                    <th width="12%">Subtotal</th>
                    <th width="15%">Reason</th>
                    <th width="20%">Remark</th>

                </tr>

                </thead>

                <tbody>

                @forelse($salesReturn->details as $detail)

                    <tr>

                        <td class="text-center">

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            <strong>

                                {{ $detail->saleDetail->product->product_name }}

                            </strong>

                            <br>

                            <small class="text-muted">

                                {{ $detail->saleDetail->product->sku }}

                            </small>

                        </td>

                        <td class="text-center">

                            {{ number_format($detail->qty,2) }}

                        </td>

                        <td class="text-right">

                            {{ number_format($detail->unit_price,2) }}

                        </td>

                        <td class="text-right">

                            {{ number_format($detail->subtotal,2) }}

                        </td>

                        <td>

                            {{ $detail->reason ?: '-' }}

                        </td>

                        <td>

                            {{ $detail->remark ?: '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center text-muted">

                            No returned items found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

                <tfoot>

                <tr>

                    <th colspan="4" class="text-right">

                        Return Total

                    </th>

                    <th class="text-right">

                        {{ number_format($salesReturn->grand_total,2) }}

                    </th>

                    <th colspan="2"></th>

                </tr>

                </tfoot>

            </table>

        </div>

    </div>

</div>

@stop