<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>Inventory Report</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:DejaVu Sans,sans-serif;
    font-size:11px;
    color:#222;
    margin:25px;
}

.header{
    border-bottom:2px solid #444;
    padding-bottom:12px;
    margin-bottom:20px;
}

.header-table{
    width:100%;
    border-collapse:collapse;
}

.logo{
    width:85px;
}

.company h2{
    margin:0;
    font-size:22px;
}

.company p{
    margin:2px 0;
    color:#555;
}

.title{
    text-align:center;
    margin:20px 0;
}

.title h3{
    margin:0;
    font-size:20px;
}

.filters{
    width:100%;
    border-collapse:collapse;
    margin-bottom:20px;
}

.filters td{
    border:1px solid #ccc;
    padding:6px 8px;
}

.filters td:first-child{
    width:170px;
    background:#efefef;
    font-weight:bold;
}

.report{
    width:100%;
    border-collapse:collapse;
}

.report th{
    border:1px solid #888;
    background:#efefef;
    padding:7px;
    text-align:center;
}

.report td{
    border:1px solid #ccc;
    padding:6px;
}

.text-center{
    text-align:center;
}

.text-right{
    text-align:right;
}

.summary{
    width:45%;
    margin-left:auto;
    margin-top:25px;
    border-collapse:collapse;
}

.summary td{
    border:1px solid #ccc;
    padding:7px;
}

.summary td:first-child{
    background:#f5f5f5;
    font-weight:bold;
}

.badge-success{
    color:#fff;
    background:#28a745;
    padding:2px 6px;
}

.badge-warning{
    color:#212529;
    background:#ffc107;
    padding:2px 6px;
}

.badge-danger{
    color:#fff;
    background:#dc3545;
    padding:2px 6px;
}

.footer{
    margin-top:35px;
    text-align:center;
    font-size:10px;
    color:#666;
}

</style>

</head>

<body>

<div class="header">

<table class="header-table">

<tr>

<td class="logo">

@if(!empty($company?->logo))

<img
    src="{{ public_path('storage/'.$company->logo) }}"
    width="75">

@endif

</td>

<td class="company">

<h2>{{ $company->company_name ?? config('app.name') }}</h2>

<p>{{ $company->address ?? '' }}</p>

<p>Phone : {{ $company->phone ?? '-' }}</p>

<p>Email : {{ $company->email ?? '-' }}</p>

</td>

</tr>

</table>

</div>

<div class="title">

<h3>Inventory Report</h3>

<div>

Generated :

{{ $generated->format('Y-m-d H:i:s') }}

</div>

</div>

<table class="filters">

<tr>
    <td>Category</td>
    <td>
        {{
            optional(
                $categories->firstWhere(
                    'id',
                    $filters['category_id'] ?? null
                )
            )->category_name ?? 'All Categories'
        }}
    </td>
</tr>

<tr>
    <td>Brand</td>
    <td>
        {{
            optional(
                $brands->firstWhere(
                    'id',
                    $filters['brand_id'] ?? null
                )
            )->brand_name ?? 'All Brands'
        }}
    </td>
</tr>

<tr>
    <td>Supplier</td>
    <td>
        {{
            optional(
                $suppliers->firstWhere(
                    'id',
                    $filters['supplier_id'] ?? null
                )
            )->company_name ?? 'All Suppliers'
        }}
    </td>
</tr>

<tr>
    <td>Stock Status</td>
    <td>{{ $filters['stock_status'] ?? 'All' }}</td>
</tr>

<tr>
    <td>Keyword</td>
    <td>{{ $filters['keyword'] ?? '-' }}</td>
</tr>

</table>

<table class="report">

<thead>

<tr>

<th>SKU</th>
<th>Product</th>
<th>Category</th>
<th>Brand</th>
<th>Unit</th>
<th>Stock</th>
<th>Avg Cost</th>
<th>Sell Price</th>
<th>Value</th>
<th>Status</th>

</tr>

</thead>

<tbody>
@forelse($products as $product)

@php

    $value = $product->stock * $product->average_cost;

    if($product->stock <= 0){

        $status = 'Out of Stock';
        $badge = 'badge-danger';

    }elseif($product->stock <= ($product->minimum_stock ?? 0)){

        $status = 'Low Stock';
        $badge = 'badge-warning';

    }else{

        $status = 'In Stock';
        $badge = 'badge-success';

    }

@endphp

<tr>

    <td>{{ $product->sku }}</td>

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

        <span class="{{ $badge }}">

            {{ $status }}

        </span>

    </td>

</tr>

@empty

<tr>

    <td colspan="10"
        class="text-center"
        style="padding:20px;">

        No inventory records found.

    </td>

</tr>

@endforelse

</tbody>

</table>

<table class="summary">

<tr>

    <td>Total Products</td>

    <td class="text-right">

        {{ number_format($summary['products']) }}

    </td>

</tr>

<tr>

    <td>Total Stock Qty</td>

    <td class="text-right">

        {{ number_format($summary['stock_qty']) }}

    </td>

</tr>

<tr>

    <td>Total Inventory Cost</td>

    <td class="text-right">

        {{ number_format($summary['inventory_cost'],2) }}

    </td>

</tr>

<tr>

    <td>Total Inventory Value</td>

    <td class="text-right">

        <strong>

            {{ number_format($summary['inventory_sale'],2) }}

        </strong>

    </td>

</tr>

<tr>

    <td>Low Stock Items</td>

    <td class="text-right">

        {{ number_format($summary['low_stock']) }}

    </td>

</tr>

<tr>

    <td>Out of Stock Items</td>

    <td class="text-right">

        {{ number_format($summary['out_of_stock']) }}

    </td>

</tr>

</table>
<div class="footer">

    <div>

        Report generated on

        {{ $generated->format('Y-m-d H:i:s') }}

    </div>

    <div style="margin-top:6px;">

        {{ $company->company_name ?? config('app.name') }}

    </div>

    <div style="margin-top:6px;">

        *** End of Inventory Report ***

    </div>

</div>

<script>

window.onload = function () {

    window.print();

};

</script>

</body>

</html>