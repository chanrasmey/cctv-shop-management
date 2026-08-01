@extends('adminlte::page')

@section('title', 'Inventory Report')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="mb-1">Inventory Report</h1>

        <small class="text-muted">
            Current Inventory Position
        </small>

    </div>

    <div>

        <a href="{{ route('reports.inventory.print', request()->query()) }}"
           target="_blank"
           class="btn btn-outline-secondary">

            <i class="fas fa-print"></i> Print

        </a>

        <a href="{{ route('reports.inventory.pdf', request()->query()) }}"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i> PDF

        </a>

        <a href="{{ route('reports.inventory.excel', request()->query()) }}"
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

                <div class="col-md-3">

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

                <div class="col-md-3">

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

                    <label>Stock Status</label>

                    <select
                        name="stock_status"
                        class="form-control">

                        <option value="">All</option>

                        <option value="in_stock"
                            @selected(($filters['stock_status'] ?? '')=='in_stock')>
                            In Stock
                        </option>

                        <option value="low_stock"
                            @selected(($filters['stock_status'] ?? '')=='low_stock')>
                            Low Stock
                        </option>

                        <option value="out_of_stock"
                            @selected(($filters['stock_status'] ?? '')=='out_of_stock')>
                            Out of Stock
                        </option>

                    </select>

                </div>

            </div>

            <div class="row mt-3">

                <div class="col-md-8">

                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        placeholder="Search Product / SKU / Barcode..."
                        value="{{ $filters['keyword'] ?? '' }}">

                </div>

                <div class="col-md-4 text-right">

                    <button class="btn btn-primary">

                        <i class="fas fa-search"></i>

                        Filter

                    </button>

                    <a
                        href="{{ route('reports.inventory.index') }}"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </div>

    </form>

</div>
<div class="row">

    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="small-box bg-info">

            <div class="inner">
                <h3>{{ number_format($summary['products']) }}</h3>
                <p>Total Products</p>
            </div>

            <div class="icon">
                <i class="fas fa-box"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="small-box bg-success">

            <div class="inner">
                <h3>{{ number_format($summary['stock_qty']) }}</h3>
                <p>Total Stock Qty</p>
            </div>

            <div class="icon">
                <i class="fas fa-cubes"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="small-box bg-primary">

            <div class="inner">
                <h3>{{ number_format($summary['inventory_cost'],2) }}</h3>
                <p>Inventory Cost</p>
            </div>

            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="small-box bg-secondary">

            <div class="inner">
                <h3>{{ number_format($summary['inventory_sale'],2) }}</h3>
                <p>Inventory Value</p>
            </div>

            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="small-box bg-warning">

            <div class="inner">
                <h3>{{ number_format($summary['low_stock']) }}</h3>
                <p>Low Stock</p>
            </div>

            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>

        </div>

    </div>

    <div class="col-lg-2 col-md-4 col-sm-6">

        <div class="small-box bg-danger">

            <div class="inner">
                <h3>{{ number_format($summary['out_of_stock']) }}</h3>
                <p>Out of Stock</p>
            </div>

            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Inventory List

        </h3>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-bordered table-hover">

            <thead>

            <tr>

                <th>SKU</th>
                <th>Barcode</th>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Unit</th>
                <th class="text-center">Stock</th>
                <th class="text-right">Avg Cost</th>
                <th class="text-right">Sell Price</th>
                <th class="text-right">Value</th>
                <th class="text-center">Status</th>

            </tr>

            </thead>

            <tbody>

            @forelse($products as $product)

                @php

                    $value = $product->stock * $product->average_cost;

                    if($product->stock <= 0){

                        $badge='danger';
                        $status='Out of Stock';

                    }elseif($product->stock <= ($product->minimum_stock ?? 0)){

                        $badge='warning';
                        $status='Low Stock';

                    }else{

                        $badge='success';
                        $status='In Stock';

                    }

                @endphp

                <tr>

                    <td>{{ $product->sku }}</td>

                    <td>{{ $product->barcode }}</td>

                    <td>{{ $product->product_name }}</td>

                    <td>{{ $product->category->category_name ?? '-' }}</td>

                    <td>{{ $product->brand->brand_name ?? '-' }}</td>

                    <td>{{ $product->unit->unit_name ?? '-' }}</td>

                    <td class="text-center">

                        {{ number_format($product->stock) }}

                    </td>

                    <td class="text-right">

                        {{ number_format($product->average_cost,2) }}

                    </td>

                    <td class="text-right">

                        {{ number_format($product->sell_price,2) }}

                    </td>

                    <td class="text-right">

                        {{ number_format($value,2) }}

                    </td>

                    <td class="text-center">

                        <span class="badge badge-{{ $badge }}">

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

            <strong>{{ $products->firstItem() ?? 0 }}</strong>

            to

            <strong>{{ $products->lastItem() ?? 0 }}</strong>

            of

            <strong>{{ $products->total() }}</strong>

            products

        </div>

        <div class="float-right">

            {{ $products->links() }}

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
    font-size:0.85rem;
    padding:.45em .7em;
}

</style>

@stop

@section('js')

<script>

$(function(){

    $('form').on('submit', function(){

        const btn = $(this).find('button[type=submit]');

        btn.prop('disabled', true);

        btn.html(
            '<i class="fas fa-spinner fa-spin"></i> Loading...'
        );

    });

});

</script>

@stop