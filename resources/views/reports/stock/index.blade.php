@extends('adminlte::page')

@section('title', 'Stock Report')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1 class="mb-0">
        <i class="fas fa-boxes text-primary mr-2"></i>
        Stock Report
    </h1>

    <div>

        <a href="{{ route('reports.stock.print', request()->query()) }}"
           target="_blank"
           class="btn btn-primary">

            <i class="fas fa-print"></i>
            Print

        </a>

        <a href="{{ route('reports.stock.excel', request()->query()) }}"
           class="btn btn-success">

            <i class="fas fa-file-excel"></i>
            Excel

        </a>

        <a href="{{ route('reports.stock.pdf', request()->query()) }}"
           target="_blank"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i>
            PDF

        </a>

    </div>

</div>

@stop

@section('content')



<section class="content">

<div class="container-fluid">

<form method="GET">

<div class="card">

<div class="card-body">

<div class="row">

<div class="col-md-3">

<label>Category</label>

<select
    name="category_id"
    class="form-control">

<option value="">All Categories</option>

@foreach($categories as $category)

<option value="{{ $category->id }}"
    @selected(($filters['category_id'] ?? '') == $category->id)>

{{ $category->name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-3">

<label>Brand</label>

<select
    name="brand_id"
    class="form-control">

<option value="">All Brands</option>

@foreach($brands as $brand)

<option value="{{ $brand->id }}"
    @selected(($filters['brand_id'] ?? '') == $brand->id)>

{{ $brand->name }}

</option>

@endforeach

</select>

</div>

<div class="col-md-4">

<label>Search</label>

<input
    type="text"
    name="search"
    value="{{ $filters['search'] ?? '' }}"
    class="form-control"
    placeholder="Product / SKU / Barcode">

</div>

<div class="col-md-2 d-flex align-items-end">

<button class="btn btn-primary btn-block">

<i class="fas fa-search"></i>

Filter

</button>

</div>

</div>

</div>

</div>

</form>

<div class="row">

<div class="col-md-4">

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

<div class="col-md-4">

<div class="small-box bg-success">

<div class="inner">

<h3>{{ number_format($summary['stock_qty']) }}</h3>

<p>Total Quantity</p>

</div>

<div class="icon">

<i class="fas fa-cubes"></i>

</div>

</div>

</div>

<div class="col-md-4">

<div class="small-box bg-warning">

<div class="inner">

<h3>${{ number_format($summary['stock_value'],2) }}</h3>

<p>Inventory Value</p>

</div>

<div class="icon">

<i class="fas fa-dollar-sign"></i>

</div>

</div>

</div>

</div>

<div class="card">

<div class="card-body p-0">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>SKU</th>
<th>Product</th>
<th>Category</th>
<th>Brand</th>
<th>Unit</th>
<th class="text-center">Stock</th>
<th class="text-right">Avg Cost</th>
<th class="text-right">Sell Price</th>
<th class="text-right">Value</th>

</tr>

</thead>

<tbody>

@forelse($products as $product)

<tr class="{{ $product->stock <= 5 ? 'table-danger' : '' }}">

<td>{{ $product->sku }}</td>

<td>{{ $product->product_name }}</td>

<td>{{ optional($product->category)->name }}</td>

<td>{{ optional($product->brand)->name }}</td>

<td>{{ optional($product->unit)->name }}</td>

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

{{ number_format($product->stock * $product->average_cost,2) }}

</td>

</tr>

@empty

<tr>

<td colspan="9" class="text-center text-muted">

No stock records found.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

@if($products instanceof \Illuminate\Contracts\Pagination\Paginator)

<div class="card-footer">

{{ $products->links() }}

</div>

@endif

</div>

</div>

</section>

@endsection