@extends('adminlte::page')

@section('title', 'Stock Valuation Report')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="mb-1">Stock Valuation Report</h1>

        <small class="text-muted">
            Inventory Valuation & Gross Profit Analysis
        </small>

    </div>

    <div>

        <a href="{{ route('reports.stock-valuation.print', request()->query()) }}"
           target="_blank"
           class="btn btn-outline-secondary">

            <i class="fas fa-print"></i> Print

        </a>

        <a href="{{ route('reports.stock-valuation.pdf', request()->query()) }}"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i> PDF

        </a>

        <a href="{{ route('reports.stock-valuation.excel', request()->query()) }}"
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

                <div class="col-md-5">

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

                <div class="col-md-5">

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

                <div class="col-md-2 d-flex align-items-end">

                    <button
                        class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>

                        Filter

                    </button>

                    <a
                        href="{{ route('reports.stock-valuation.index') }}"
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
                <h3>{{ number_format($summary['total_products']) }}</h3>
                <p>Total Products</p>
            </div>

            <div class="icon">
                <i class="fas fa-box"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-primary">

            <div class="inner">
                <h3>{{ number_format($summary['total_qty']) }}</h3>
                <p>Total Quantity</p>
            </div>

            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">
                <h3>${{ number_format($summary['cost_value'], 2) }}</h3>
                <p>Inventory Cost Value</p>
            </div>

            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning">

            <div class="inner">
                <h3>${{ number_format($summary['gross_profit'], 2) }}</h3>
                <p>Expected Gross Profit</p>
            </div>

            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Inventory Valuation

        </h3>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-bordered table-hover">

            <thead>

            <tr>

                <th>SKU</th>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th class="text-center">Stock</th>
                <th class="text-right">Average Cost</th>
                <th class="text-right">Cost Value</th>
                <th class="text-right">Selling Price</th>
                <th class="text-right">Selling Value</th>
                <th class="text-right">Gross Profit</th>
                <th class="text-center">Status</th>

            </tr>

            </thead>

            <tbody>

            @forelse($items as $item)

                @php

                    $costValue = $item->stock * $item->average_cost;

                    $sellingValue = $item->stock * $item->sell_price;

                    $profit = $sellingValue - $costValue;

                    $status = app(\App\Services\Reports\StockValuationReportService::class)
                        ->stockStatus($item);

                    $color = app(\App\Services\Reports\StockValuationReportService::class)
                        ->stockStatusColor($item);

                @endphp

                <tr>

                    <td>{{ $item->sku }}</td>

                    <td>{{ $item->product_name }}</td>

                    <td>{{ $item->category->category_name ?? '-' }}</td>

                    <td>{{ $item->brand->brand_name ?? '-' }}</td>

                    <td class="text-center">
                        {{ number_format($item->stock) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($item->average_cost, 2) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($costValue, 2) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($item->sell_price, 2) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($sellingValue, 2) }}
                    </td>

                    <td class="text-right">
                        {{ number_format($profit, 2) }}
                    </td>

                    <td class="text-center">

                        <span class="badge badge-{{ $color }}">

                            {{ $status }}

                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="11" class="text-center py-4">

                        No inventory records found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>
        <div class="card-footer clearfix">

        <div class="float-left mt-2">

            Showing

            <strong>{{ $items->firstItem() ?? 0 }}</strong>

            to

            <strong>{{ $items->lastItem() ?? 0 }}</strong>

            of

            <strong>{{ $items->total() }}</strong>

            product(s)

        </div>

        <div class="float-right">

            {{ $items->links() }}

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

.text-right{
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