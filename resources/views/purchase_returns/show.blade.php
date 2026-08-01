@extends('adminlte::page')

@section('title', 'Purchase Return '.$purchaseReturn->return_no)

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>

        Purchase Return {{ $purchaseReturn->return_no }}

    </h1>

    <div>

        @if($purchaseReturn->isCompleted())

            <a
                href="{{ route('purchase-returns.print', $purchaseReturn) }}"
                target="_blank"
                class="btn btn-primary">

                <i class="fas fa-print"></i>

                Print

            </a>

            <form
                action="{{ route('purchase-returns.cancel', $purchaseReturn) }}"
                method="POST"
                class="d-inline"
                onsubmit="return confirm('Cancel this Purchase Return? Stock will be restored.')">

                @csrf
                @method('PATCH')

                <button
                    class="btn btn-danger"
                    type="submit">

                    <i class="fas fa-ban"></i>

                    Cancel

                </button>

            </form>

        @endif

        <a
            href="{{ route('purchases.show', $purchaseReturn->purchase) }}"
            class="btn btn-secondary">

            Back

        </a>

    </div>

</div>

@stop

@section('content')

@if(session('success'))

<div class="alert alert-success alert-dismissible">

    <button
        class="close"
        data-dismiss="alert">

        &times;

    </button>

    {{ session('success') }}

</div>

@endif

@if(session('error'))

<div class="alert alert-danger alert-dismissible">

    <button
        class="close"
        data-dismiss="alert">

        &times;

    </button>

    {{ session('error') }}

</div>

@endif

<div class="card">

    <div class="card-header bg-danger">

        <h3 class="card-title">

            Return Information

        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3">

                <strong>Return No</strong>

                <p>

                    {{ $purchaseReturn->return_no }}

                </p>

            </div>

            <div class="col-md-3">

                <strong>Date</strong>

                <p>

                    {{ $purchaseReturn->return_date->format('d-m-Y') }}

                </p>

            </div>

            <div class="col-md-3">

                <strong>Purchase No</strong>

                <p>

                    {{ $purchaseReturn->purchase->purchase_no }}

                </p>

            </div>

            <div class="col-md-3">

                <strong>Status</strong>

                <p>

                    {!! $purchaseReturn->status_badge !!}

                </p>

            </div>

        </div>

        <div class="row">

            <div class="col-md-6">

                <strong>Supplier</strong>

                <p>

                    {{ $purchaseReturn->supplier->company_name }}

                </p>

            </div>

            <div class="col-md-6">

                <strong>Remark</strong>

                <p>

                    {{ $purchaseReturn->remark ?: '-' }}

                </p>

            </div>

        </div>

    </div>

</div>
<div class="card">

    <div class="card-header bg-success">

        <h3 class="card-title">

            Returned Items

        </h3>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead>

                <tr>

                    <th width="50">#</th>

                    <th>SKU</th>

                    <th>Product</th>

                    <th class="text-end" width="120">

                        Qty

                    </th>

                    <th class="text-end" width="140">

                        Unit Cost

                    </th>

                    <th class="text-end" width="150">

                        Subtotal

                    </th>

                    <th>

                        Reason

                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($purchaseReturn->details as $detail)

                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            {{ $detail->product?->sku }}

                        </td>

                        <td>

                            {{ $detail->product?->product_name }}

                        </td>

                        <td class="text-end">

                            {{ number_format($detail->qty,2) }}

                        </td>

                        <td class="text-end">

                            {{ number_format($detail->unit_cost,2) }}

                        </td>

                        <td class="text-end">

                            {{ number_format($detail->subtotal,2) }}

                        </td>

                        <td>

                            {{ $detail->reason ?: '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center text-muted">

                            No returned items.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
<div class="card">

    <div class="card-header bg-warning">

        <h3 class="card-title">

            Return Summary

        </h3>

    </div>

    <div class="card-body">

        <div class="row justify-content-end">

            <div class="col-md-5">

                <table class="table table-bordered">

                    <tr>

                        <th width="45%">

                            Return Total

                        </th>

                        <td class="text-end font-weight-bold">

                            {{ number_format($purchaseReturn->subtotal, 2) }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Created By

                        </th>

                        <td>

                            {{ $purchaseReturn->user?->name ?? '-' }}

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Created At

                        </th>

                        <td>

                            {{ $purchaseReturn->created_at?->format('d-m-Y H:i') }}

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>

@stop