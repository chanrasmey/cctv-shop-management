<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>Purchase Report</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family: DejaVu Sans, sans-serif;
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
    width:150px;
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
    width:42%;
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

.footer{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
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

<h3>Purchase Report</h3>

<div>

Generated :

{{ $generated->format('Y-m-d H:i:s') }}

</div>

</div>

<table class="filters">

<tr>

<td>Date From</td>

<td>{{ $filters['date_from'] ?? 'All' }}</td>

</tr>

<tr>

<td>Date To</td>

<td>{{ $filters['date_to'] ?? 'All' }}</td>

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
)->company_name
?? 'All Suppliers'
}}

</td>

</tr>

<tr>

<td>Status</td>

<td>{{ $filters['status'] ?? 'All Status' }}</td>

</tr>

</table>

<table class="report">

<thead>

<tr>

<th>Date</th>

<th>Purchase No</th>

<th>Supplier</th>

<th>Items</th>

<th>Grand Total</th>

<th>Paid</th>

<th>Balance</th>

<th>Status</th>

</tr>

</thead>

<tbody>
@forelse($purchases as $purchase)

<tr>

    <td class="text-center">
        {{ optional($purchase->purchase_date)->format('Y-m-d') }}
    </td>

    <td>
        {{ $purchase->purchase_no }}
    </td>

    <td>
        {{ $purchase->supplier->company_name ?? '-' }}
    </td>

    <td class="text-center">
        {{ $purchase->details->sum('qty') }}
    </td>

    <td class="text-right">
        {{ number_format($purchase->grand_total,2) }}
    </td>

    <td class="text-right">
        {{ number_format($purchase->paid_amount,2) }}
    </td>

    <td class="text-right">
        {{ number_format($purchase->balance,2) }}
    </td>

    <td class="text-center">
        {{ $purchase->status }}
    </td>

</tr>

@empty

<tr>

    <td colspan="8"
        class="text-center"
        style="padding:20px;">

        No purchase records found.

    </td>

</tr>

@endforelse

</tbody>

</table>

<table class="summary">

<tr>
    <td>Total Purchases</td>
    <td class="text-right">
        {{ number_format($summary['purchase_count']) }}
    </td>
</tr>

<tr>
    <td>Subtotal</td>
    <td class="text-right">
        {{ number_format($summary['subtotal'],2) }}
    </td>
</tr>

<tr>
    <td>Discount</td>
    <td class="text-right">
        {{ number_format($summary['discount'],2) }}
    </td>
</tr>

<tr>
    <td>Tax</td>
    <td class="text-right">
        {{ number_format($summary['tax'],2) }}
    </td>
</tr>

<tr>
    <td>Grand Total</td>
    <td class="text-right">
        <strong>
            {{ number_format($summary['grand_total'],2) }}
        </strong>
    </td>
</tr>

<tr>
    <td>Paid</td>
    <td class="text-right">
        {{ number_format($summary['paid'],2) }}
    </td>
</tr>

<tr>
    <td>Balance</td>
    <td class="text-right">
        {{ number_format($summary['balance'],2) }}
    </td>
</tr>

<tr>
    <td>Return Total</td>
    <td class="text-right">
        {{ number_format($summary['return_total'],2) }}
    </td>
</tr>

<tr>

    <td>
        <strong>Net Purchase Total</strong>
    </td>

    <td class="text-right">

        <strong>

            {{ number_format($summary['net_total'],2) }}

        </strong>

    </td>

</tr>

</table>
<div class="footer">

    <div>
        Generated on {{ $generated->format('Y-m-d H:i:s') }}
    </div>

    <div style="margin-top:4px;">
        {{ $company->company_name ?? config('app.name') }}
    </div>

    <div style="margin-top:4px;">
        Purchase Report
    </div>

</div>

</body>

</html>