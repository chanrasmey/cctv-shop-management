@extends('adminlte::page')

@section('title', 'Purchase Return '.$purchaseReturn->return_no)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Purchase Return {{ $purchaseReturn->return_no }}</h1>

        <div>
            <a href="{{ route('purchases.show', $purchaseReturn->purchase) }}" class="btn btn-secondary">
                Back to Purchase
            </a>
        </div>
    </div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">
            &times;
        </button>

        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">Return Information</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Return No</strong>
                <p>{{ $purchaseReturn->return_no }}</p>
            </div>

            <div class="col-md-3">
                <strong>Return Date</strong>
                <p>{{ $purchaseReturn->return_date->format('d-m-Y') }}</p>
            </div>

            <div class="col-md-3">
                <strong>Purchase No</strong>
                <p>{{ $purchaseReturn->purchase?->purchase_no }}</p>
            </div>

            <div class="col-md-3">
                <strong>Supplier</strong>
                <p>{{ $purchaseReturn->supplier?->company_name }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <strong>Status</strong>
                <p><span class="badge badge-success">{{ $purchaseReturn->status }}</span></p>
            </div>

            <div class="col-md-9">
                <strong>Remark</strong>
                <p>{{ $purchaseReturn->remark ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning">
        <h3 class="card-title">Return Items</h3>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>SKU</th>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit Cost</th>
                    <th class="text-end">Subtotal</th>
                    <th>Reason</th>
                </tr>
            </thead>

            <tbody>
                @foreach($purchaseReturn->details as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->product?->sku }}</td>
                        <td>{{ $detail->product?->product_name }}</td>
                        <td class="text-end">{{ number_format($detail->qty, 2) }}</td>
                        <td class="text-end">{{ number_format($detail->unit_cost, 2) }}</td>
                        <td class="text-end">{{ number_format($detail->subtotal, 2) }}</td>
                        <td>{{ $detail->reason ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="table-primary">
                    <th colspan="5" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($purchaseReturn->subtotal, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@stop
