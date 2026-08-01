<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Stock Report</title>

    <style>

        body{
            font-family: DejaVu Sans,sans-serif;
            font-size:12px;
            color:#222;
        }

        h2,h3,p{
            margin:0;
        }

        .header{
            text-align:center;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#efefef;
            border:1px solid #999;
            padding:6px;
        }

        td{
            border:1px solid #999;
            padding:5px;
        }

        .right{
            text-align:right;
        }

        .center{
            text-align:center;
        }

        .footer{
            margin-top:20px;
            font-size:11px;
            text-align:right;
        }

    </style>

</head>

<body onload="window.print()">

<div class="header">

    @if(!empty($company?->logo) && file_exists(public_path('storage/'.$company->logo)))
        <img src="{{ public_path('storage/'.$company->logo) }}"
             style="height:70px;">
    @endif

    <h2>{{ $company->company_name ?? config('app.name') }}</h2>

    <p>{{ $company->address ?? '' }}</p>

    <p>{{ $company->phone ?? '' }}</p>

    <br>

    <h3>STOCK REPORT</h3>

</div>

<table>

<thead>

<tr>

<th>SKU</th>
<th>Product</th>
<th>Category</th>
<th>Brand</th>
<th>Stock</th>
<th>Average Cost</th>
<th>Selling Price</th>
<th>Stock Value</th>

</tr>

</thead>

<tbody>

@forelse($products as $product)

<tr>

<td>{{ $product->sku }}</td>

<td>{{ $product->product_name }}</td>

<td>{{ optional($product->category)->name }}</td>

<td>{{ optional($product->brand)->name }}</td>

<td class="center">{{ number_format($product->stock) }}</td>

<td class="right">{{ number_format($product->average_cost,2) }}</td>

<td class="right">{{ number_format($product->sell_price,2) }}</td>

<td class="right">

{{ number_format($product->stock * $product->average_cost,2) }}

</td>

</tr>

@empty

<tr>

<td colspan="8" class="center">

No records found.

</td>

</tr>

@endforelse

</tbody>

<tfoot>

<tr>

<th colspan="4" class="right">

TOTAL

</th>

<th class="center">

{{ number_format($summary['stock_qty']) }}

</th>

<th></th>

<th></th>

<th class="right">

{{ number_format($summary['stock_value'],2) }}

</th>

</tr>

</tfoot>

</table>

<div class="footer">

Generated :

{{ now()->format('Y-m-d H:i:s') }}

</div>

</body>

</html>