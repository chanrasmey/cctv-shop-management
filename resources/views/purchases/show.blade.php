@extends('adminlte::page')

@section('title', 'Purchase '.$purchase->purchase_no)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Purchase {{ $purchase->purchase_no }}</h1>

        <div>
            @if($purchase->isDraft() || $purchase->isPending())
                <a
                    href="{{ route('purchases.edit', $purchase) }}"
                    class="btn btn-warning">

                    <i class="fas fa-edit"></i>
                    Edit
                </a>
            @endif

            @if($purchase->isCompleted())
                <a
                    href="{{ route('purchases.returns.create', $purchase) }}"
                    class="btn btn-danger">

                    <i class="fas fa-undo"></i>
                    Return
                </a>
            @endif

            @if(! $purchase->isCancelled())
                <form
                    action="{{ route('purchases.cancel', $purchase) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Cancel this purchase? Completed purchases will reverse stock.')">

                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i>
                        Cancel
                    </button>
                </form>
            @endif

            <a
                href="{{ route('purchases.index') }}"
                class="btn btn-secondary">

                Back
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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">
            &times;
        </button>

        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">Purchase Information</h3>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Purchase No</strong>
                <p>{{ $purchase->purchase_no }}</p>
            </div>

            <div class="col-md-3">
                <strong>Date</strong>
                <p>{{ $purchase->purchase_date->format('d-m-Y') }}</p>
            </div>

            <div class="col-md-3">
                <strong>Supplier</strong>
                <p>{{ $purchase->supplier?->company_name }}</p>
            </div>

            <div class="col-md-3">
                <strong>Status</strong>
                <p>
                    @switch($purchase->status)
                        @case('Completed')
                            <span class="badge badge-success">Completed</span>
                            @break
                        @case('Pending')
                            <span class="badge badge-warning">Pending</span>
                            @break
                        @case('Cancelled')
                            <span class="badge badge-danger">Cancelled</span>
                            @break
                        @default
                            <span class="badge badge-secondary">Draft</span>
                    @endswitch
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <strong>Invoice No</strong>
                <p>{{ $purchase->invoice_no ?: '-' }}</p>
            </div>

            <div class="col-md-9">
                <strong>Remark</strong>
                <p>{{ $purchase->remark ?: '-' }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-success">
        <h3 class="card-title">Purchase Items</h3>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>SKU</th>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Returned</th>
                    <th class="text-end">Returnable</th>
                    <th class="text-end">Unit Cost</th>
                    <th class="text-end">Discount</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach($purchase->details as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->product?->sku }}</td>
                        <td>{{ $detail->product?->product_name }}</td>
                        <td class="text-end">{{ number_format($detail->qty, 2) }}</td>
                        <td class="text-end">{{ number_format($detail->returnedQty(), 2) }}</td>
                        <td class="text-end">{{ number_format($detail->returnableQty(), 2) }}</td>
                        <td class="text-end">{{ number_format($detail->unit_cost, 2) }}</td>
                        <td class="text-end">{{ number_format($detail->discount_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning">
        <h3 class="card-title">Purchase Summary</h3>
    </div>

    <div class="card-body">
        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-bordered">
                    <tr>
                        <th>Subtotal</th>
                        <td class="text-end">{{ number_format($purchase->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Discount</th>
                        <td class="text-end">{{ number_format($purchase->discount_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Tax</th>
                        <td class="text-end">{{ number_format($purchase->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="table-primary">
                        <th>Grand Total</th>
                        <td class="text-end font-weight-bold">{{ number_format($purchase->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Paid Amount</th>
                        <td class="text-end">{{ number_format($purchase->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="table-danger">
                        <th>Balance</th>
                        <td class="text-end font-weight-bold">{{ number_format($purchase->balance, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@if($purchase->returns->isNotEmpty())
    <div class="card">
        <div class="card-header bg-danger">
            <h3 class="card-title">Purchase Returns</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Return No</th>
                        <th>Date</th>
                        <th class="text-end">Items</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($purchase->returns as $purchaseReturn)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchaseReturn->return_no }}</td>
                            <td>{{ $purchaseReturn->return_date->format('d-m-Y') }}</td>
                            <td class="text-end">{{ $purchaseReturn->details->count() }}</td>
                            <td class="text-end">{{ number_format($purchaseReturn->subtotal, 2) }}</td>
                            <td>
                                <span class="badge badge-success">{{ $purchaseReturn->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('purchase-returns.show', $purchaseReturn) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@stop
