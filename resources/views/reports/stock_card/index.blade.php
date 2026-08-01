@extends('adminlte::page')

@section('title', 'Stock Card Report')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="mb-1">Stock Card Report</h1>

        <small class="text-muted">
            Inventory Movement History
        </small>

    </div>

    <div>

        <a href="{{ route('reports.stock-card.print', request()->query()) }}"
           target="_blank"
           class="btn btn-outline-secondary">

            <i class="fas fa-print"></i> Print

        </a>

        <a href="{{ route('reports.stock-card.pdf', request()->query()) }}"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i> PDF

        </a>

        <a href="{{ route('reports.stock-card.excel', request()->query()) }}"
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

            Filters

        </h3>

    </div>

    <form method="GET">

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <label>Product</label>

                    <select
                        name="product_id"
                        class="form-control">

                        <option value="">
                            All Products
                        </option>

                        @foreach($products as $product)

                            <option
                                value="{{ $product->id }}"
                                @selected(($filters['product_id'] ?? '') == $product->id)>

                                {{ $product->product_name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-4">

                    <label>Category</label>

                    <select
                        name="category_id"
                        class="form-control">

                        <option value="">
                            All Categories
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                @selected(($filters['category_id'] ?? '') == $category->id)>

                                {{ $category->category_name }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-4">

                    <label>Brand</label>

                    <select
                        name="brand_id"
                        class="form-control">

                        <option value="">
                            All Brands
                        </option>

                        @foreach($brands as $brand)

                            <option
                                value="{{ $brand->id }}"
                                @selected(($filters['brand_id'] ?? '') == $brand->id)>

                                {{ $brand->brand_name }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="row mt-3">

                <div class="col-md-3">

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

                <div class="col-md-3">

                    <label>Date From</label>

                    <input
                        type="date"
                        name="date_from"
                        class="form-control"
                        value="{{ $filters['date_from'] ?? '' }}">

                </div>

                <div class="col-md-3">

                    <label>Date To</label>

                    <input
                        type="date"
                        name="date_to"
                        class="form-control"
                        value="{{ $filters['date_to'] ?? '' }}">

                </div>

                <div class="col-md-3 d-flex align-items-end">

                    <button
                        class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>

                        Filter

                    </button>

                    <a
                        href="{{ route('reports.stock-card.index') }}"
                        class="btn btn-secondary">

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
                <h3>{{ number_format($summary['opening']) }}</h3>
                <p>Opening Stock</p>
            </div>

            <div class="icon">
                <i class="fas fa-history"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">
                <h3>{{ number_format($summary['stock_in']) }}</h3>
                <p>Total Stock In</p>
            </div>

            <div class="icon">
                <i class="fas fa-arrow-down"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-danger">

            <div class="inner">
                <h3>{{ number_format($summary['stock_out']) }}</h3>
                <p>Total Stock Out</p>
            </div>

            <div class="icon">
                <i class="fas fa-arrow-up"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-primary">

            <div class="inner">
                <h3>{{ number_format($summary['closing']) }}</h3>
                <p>Closing Stock</p>
            </div>

            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Stock Movement History

        </h3>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-bordered table-hover">

            <thead>

            <tr>

                <th>Date</th>
                <th>Product</th>
                <th>Reference</th>
                <th>Type</th>
                <th class="text-center">In</th>
                <th class="text-center">Out</th>
                <th class="text-center">Balance</th>
                <th>Remark</th>
                <th>User</th>

            </tr>

            </thead>

            <tbody>

            @forelse($movements as $movement)

                @php

                    $inQty = $movement->movement_type === 'IN'
                        ? $movement->quantity
                        : 0;

                    $outQty = $movement->movement_type === 'OUT'
                        ? $movement->quantity
                        : 0;

                    $badge = match($movement->movement_type){

                        'IN' => 'success',
                        'OUT' => 'danger',
                        default => 'secondary',

                    };

                @endphp

                <tr>

                    <td>

                        {{ optional($movement->movement_date)->format('Y-m-d') }}

                    </td>

                    <td>

                        {{ $movement->product->product_name ?? '-' }}

                    </td>

                    <td>

                        {{ $movement->reference_no ?? '-' }}

                    </td>

                    <td class="text-center">

                        <span class="badge badge-{{ $badge }}">

                            {{ $movement->movement_type }}

                        </span>

                    </td>

                    <td class="text-center text-success">

                        {{ $inQty ? number_format($inQty) : '-' }}

                    </td>

                    <td class="text-center text-danger">

                        {{ $outQty ? number_format($outQty) : '-' }}

                    </td>

                    <td class="text-center">

                        <strong>

                            {{ number_format($movement->running_balance) }}

                        </strong>

                    </td>

                    <td>

                        {{ $movement->remark ?? '-' }}

                    </td>

                    <td>

                        {{ $movement->createdBy->name ?? '-' }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9"
                        class="text-center py-4">

                        No stock movement records found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>
        <div class="card-footer clearfix">

        <div class="float-left mt-2">

            Showing

            <strong>{{ $movements->firstItem() ?? 0 }}</strong>

            to

            <strong>{{ $movements->lastItem() ?? 0 }}</strong>

            of

            <strong>{{ $movements->total() }}</strong>

            movement(s)

        </div>

        <div class="float-right">

            {{ $movements->links() }}

        </div>

    </div>

</div>

@stop

@section('css')

<style>

.small-box .inner h3{
    font-size:1.8rem;
}

.table td,
.table th{
    vertical-align:middle;
}

.badge{
    font-size:.85rem;
    padding:.45em .70em;
}

.text-success{
    font-weight:600;
}

.text-danger{
    font-weight:600;
}

</style>

@stop

@section('js')

<script>

$(function () {

    $('form').on('submit', function () {

        const btn = $(this).find('button[type=submit]');

        btn.prop('disabled', true);

        btn.html(
            '<i class="fas fa-spinner fa-spin"></i> Loading...'
        );

    });

});

</script>

@stop