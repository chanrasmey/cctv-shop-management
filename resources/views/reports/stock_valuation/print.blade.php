<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Stock Valuation Report</title>

    <style>

        body{
            font-family: DejaVu Sans,sans-serif;
            font-size:12px;
            color:#222;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,
        td{
            border:1px solid #555;
            padding:6px;
        }

        th{
            background:#efefef;
            text-align:center;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .company{
            font-size:22px;
            font-weight:bold;
        }

        .title{
            font-size:18px;
            font-weight:bold;
            margin-top:8px;
        }

        .muted{
            color:#666;
        }

        .summary{
            margin:15px 0;
        }

        .summary td{
            width:25%;
            text-align:center;
            font-weight:bold;
        }

    </style>

</head>

<body>

<div>

    <div class="company">

        {{ $company->company_name ?? config('app.name') }}

    </div>

    <div>

        {{ $company->address ?? '' }}

    </div>

    <div>

        {{ $company->phone ?? '' }}

        @if(!empty($company->email))
            | {{ $company->email }}
        @endif

    </div>

    <div class="title">

        STOCK VALUATION REPORT

    </div>

    <div class="muted">

        Generated :
        {{ now()->format('Y-m-d H:i:s') }}

    </div>

</div>

<table class="summary">

    <tr>

        <td>

            Products<br>

            {{ number_format($summary['total_products']) }}

        </td>

        <td>

            Quantity<br>

            {{ number_format($summary['total_qty']) }}

        </td>

        <td>

            Cost Value<br>

            {{ number_format($summary['cost_value'],2) }}

        </td>

        <td>

            Gross Profit<br>

            {{ number_format($summary['gross_profit'],2) }}

        </td>

    </tr>

</table>

<table>

    <thead>

    <tr>

        <th>SKU</th>
        <th>Product</th>
        <th>Category</th>
        <th>Brand</th>
        <th width="8%">Stock</th>
        <th width="10%">Avg Cost</th>
        <th width="12%">Cost Value</th>
        <th width="10%">Sell Price</th>
        <th width="12%">Sell Value</th>
        <th width="12%">Profit</th>

    </tr>

    </thead>

    <tbody>
    @forelse($items as $item)

    @php

        $costValue = $item->stock * $item->average_cost;

        $sellingValue = $item->stock * $item->sell_price;

        $profit = $sellingValue - $costValue;

    @endphp

    <tr>

        <td class="text-center">

            {{ $item->sku }}

        </td>

        <td>

            {{ $item->product_name }}

        </td>

        <td>

            {{ $item->category->category_name ?? '-' }}

        </td>

        <td>

            {{ $item->brand->brand_name ?? '-' }}

        </td>

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

    </tr>

@empty

    <tr>

        <td colspan="10" class="text-center">

            No inventory valuation records found.

        </td>

    </tr>

@endforelse

    </tbody>

    <tfoot>

    <tr>

        <th colspan="4" class="text-right">

            Totals

        </th>

        <th class="text-center">

            {{ number_format($summary['total_qty']) }}

        </th>

        <th></th>

        <th class="text-right">

            {{ number_format($summary['cost_value'], 2) }}

        </th>

        <th></th>

        <th class="text-right">

            {{ number_format($summary['selling_value'], 2) }}

        </th>

        <th class="text-right">

            {{ number_format($summary['gross_profit'], 2) }}

        </th>

    </tr>

    </tfoot>

</table>

<br><br>

<table style="border:none;">

    <tr>

        <td style="border:none;width:33%;text-align:center;">

            _______________________<br>
            Prepared By

        </td>

        <td style="border:none;width:33%;text-align:center;">

            _______________________<br>
            Checked By

        </td>

        <td style="border:none;width:33%;text-align:center;">

            _______________________<br>
            Approved By

        </td>

    </tr>

</table>
<div style="margin-top:20px;">

    <table style="border:none;width:100%;">

        <tr>

            <td style="border:none;">

                Generated by:
                {{ auth()->user()->name ?? 'System' }}

            </td>

            <td style="border:none;text-align:right;">

                Printed:
                {{ now()->format('Y-m-d H:i:s') }}

            </td>

        </tr>

    </table>

</div>

<script>

window.onload = function () {

    window.print();

};

</script>

</body>

</html>