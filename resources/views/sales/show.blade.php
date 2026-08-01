@extends('adminlte::page')

@section('title', 'Sale Details')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>

        <i class="fas fa-file-invoice-dollar mr-2"></i>

        Sale Details

    </h1>

    <div>

        <a href="{{ route('sales.index') }}"
           class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

        <a href="{{ route('sales.edit', $sale) }}"
           class="btn btn-warning">

            <i class="fas fa-edit"></i>

            Edit

        </a>

        <a href="{{ route('sales.print', $sale) }}"
           target="_blank"
           class="btn btn-primary">

            <i class="fas fa-print"></i>

            Print Invoice

        </a>

    </div>

</div>

@stop

@section('content')

<div class="row">

<div class="col-md-6">

<div class="card card-primary">

<div class="card-header">

<h3 class="card-title">

Sale Information

</h3>

</div>

<div class="card-body">

<table class="table table-sm table-borderless">

<tr>

<th width="35%">

Invoice No

</th>

<td>

{{ $sale->invoice_no }}

</td>

</tr>

<tr>

<th>

Sale Date

</th>

<td>

{{ optional($sale->sale_date)->format('d M Y') }}

</td>

</tr>

<tr>

<th>

Status

</th>

<td>

<span class="badge badge-success">

{{ ucfirst($sale->status) }}

</span>

</td>

</tr>

<tr>

<th>

Payment Method

</th>

<td>

{{ ucfirst($sale->payment_method) }}

</td>

</tr>

<tr>

<th>

Created By

</th>

<td>

{{ optional($sale->user)->name }}

</td>

</tr>

</table>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card card-info">

<div class="card-header">

<h3 class="card-title">

Customer Information

</h3>

</div>

<div class="card-body">

<table class="table table-sm table-borderless">

<tr>

<th width="35%">

Customer

</th>

<td>

{{ optional($sale->customer)->name ?? 'Walk-in Customer' }}

</td>

</tr>

<tr>

<th>

Code

</th>

<td>

{{ optional($sale->customer)->customer_code }}

</td>

</tr>

<tr>

<th>

Phone

</th>

<td>

{{ optional($sale->customer)->phone }}

</td>

</tr>

<tr>

<th>

Address

</th>

<td>

{{ optional($sale->customer)->address }}

</td>

</tr>

</table>

</div>

</div>

</div>

</div>
<div class="card card-success">

    <div class="card-header">

        <h3 class="card-title">

            Sale Items

        </h3>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead>

                <tr>

                    <th width="5%">#</th>

                    <th>Product</th>

                    <th>Barcode</th>

                    <th class="text-center">Qty</th>

                    <th class="text-right">Price</th>

                    <th class="text-right">Discount</th>

                    <th class="text-right">Subtotal</th>

                </tr>

                </thead>

                <tbody>

                @forelse($sale->details as $item)

                    <tr>

                        <td class="text-center">

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            <strong>

                                {{ $item->product->name }}

                            </strong>

                            <br>

                            <small class="text-muted">

                                {{ $item->product->code }}

                            </small>

                        </td>

                        <td>

                            {{ $item->product->barcode }}

                        </td>

                        <td class="text-center">

                            {{ number_format($item->quantity,2) }}

                        </td>

                        <td class="text-right">

                            {{ number_format($item->price,2) }}

                        </td>

                        <td class="text-right">

                            {{ number_format($item->discount,2) }}

                        </td>

                        <td class="text-right font-weight-bold">

                            {{ number_format($item->subtotal,2) }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center text-muted">

                            No items found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
<div class="row">

    <div class="col-md-6">

        <div class="card card-secondary">

            <div class="card-header">

                <h3 class="card-title">

                    Remarks

                </h3>

            </div>

            <div class="card-body">

                {!! nl2br(e($sale->notes ?? '-')) !!}

            </div>

        </div>

    </div>

    <div class="col-md-6">

        <div class="card card-warning">

            <div class="card-header">

                <h3 class="card-title">

                    Payment Summary

                </h3>

            </div>

            <div class="card-body p-0">

                <table class="table table-bordered table-striped mb-0">

                    <tr>

                        <th width="50%">

                            Sub Total

                        </th>

                        <td class="text-right">

                            {{ number_format($sale->subtotal, 2) }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Discount

                        </th>

                        <td class="text-right">

                            {{ number_format($sale->discount, 2) }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Grand Total

                        </th>

                        <td class="text-right font-weight-bold text-primary">

                            {{ number_format($sale->grand_total, 2) }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Paid Amount

                        </th>

                        <td class="text-right text-success">

                            {{ number_format($sale->paid_amount, 2) }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Balance / Due

                        </th>

                        <td class="text-right {{ $sale->balance > 0 ? 'text-danger' : 'text-success' }} font-weight-bold">

                            {{ number_format($sale->balance, 2) }}

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

@stop