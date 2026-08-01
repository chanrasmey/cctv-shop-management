<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Purchase Return Report</title>

<style>

*{
    box-sizing:border-box;
}

body{
    font-family:DejaVu Sans,Arial,sans-serif;
    font-size:12px;
    color:#222;
    margin:30px;
}

.header{
    border-bottom:2px solid #444;
    padding-bottom:15px;
    margin-bottom:20px;
}

.header-table{
    width:100%;
    border-collapse:collapse;
}

.logo{
    width:90px;
    vertical-align:top;
}

.company{
    vertical-align:top;
}

.company h2{
    margin:0;
    font-size:24px;
}

.company p{
    margin:2px 0;
    color:#666;
}

.title{
    text-align:center;
    margin:20px 0;
}

.title h3{
    margin:0;
    font-size:22px;
}

.filters{
    width:100%;
    border-collapse:collapse;
    margin-bottom:20px;
}

.filters td{
    border:1px solid #ddd;
    padding:6px 10px;
}

.filters td:first-child{
    width:170px;
    background:#f5f5f5;
    font-weight:bold;
}

.report{
    width:100%;
    border-collapse:collapse;
}

.report th{
    border:1px solid #999;
    background:#efefef;
    padding:8px;
    text-align:center;
}

.report td{
    border:1px solid #ccc;
    padding:7px;
}

.text-center{
    text-align:center;
}

.text-right{
    text-align:right;
}

.badge{
    display:inline-block;
    padding:3px 8px;
    border-radius:3px;
    font-size:11px;
    font-weight:bold;
    color:#fff;
}

.success{background:#28a745;}
.warning{background:#ffc107;color:#000;}
.danger{background:#dc3545;}
.secondary{background:#6c757d;}

.summary{
    width:40%;
    margin-left:auto;
    margin-top:25px;
    border-collapse:collapse;
}

.summary td{
    border:1px solid #ddd;
    padding:8px;
}

.summary td:first-child{
    background:#f8f8f8;
    font-weight:bold;
}

.footer{
    margin-top:40px;
    font-size:11px;
    color:#777;
    text-align:center;
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
width="80">

@endif

</td>

<td class="company">

<h2>{{ $company->company_name ?? config('app.name') }}</h2>

<p>{{ $company->address ?? '' }}</p>

<p>Tel : {{ $company->phone ?? '-' }}</p>

<p>Email : {{ $company->email ?? '-' }}</p>

</td>

</tr>

</table>

</div>

<div class="title">

<h3>Purchase Return Report</h3>

<small>

Generated :

{{ now()->format('Y-m-d H:i') }}

</small>

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
<th>Return No</th>
<th>Purchase No</th>
<th>Supplier</th>
<th>Qty</th>
<th>Grand Total</th>
<th>Refund</th>
<th>Status</th>

</tr>

</thead>

<tbody>
@forelse($returns as $return)

<tr>

    <td class="text-center">
        {{ optional($return->return_date)->format('Y-m-d') }}
    </td>

    <td>
        {{ $return->return_no }}
    </td>

    <td>
        {{ $return->purchase->purchase_no ?? '-' }}
    </td>

    <td>
        {{ $return->supplier->company_name ?? '-' }}
    </td>

    <td class="text-center">
        {{ $return->details->sum('qty') }}
    </td>

    <td class="text-right">
        {{ number_format($return->grand_total,2) }}
    </td>

    <td class="text-right">
        {{ number_format($return->refund_amount,2) }}
    </td>

    <td class="text-center">

        @php
            $badge = match($return->status){
                'Completed' => 'success',
                'Pending'   => 'warning',
                'Cancelled' => 'danger',
                default     => 'secondary',
            };
        @endphp

        <span class="badge {{ $badge }}">
            {{ $return->status }}
        </span>

    </td>

</tr>

@empty

<tr>

    <td colspan="8"
        class="text-center"
        style="padding:25px;">

        No purchase return records found.

    </td>

</tr>

@endforelse

</tbody>

</table>

<table class="summary">

    <tr>
        <td>Total Returns</td>
        <td class="text-right">
            {{ number_format($summary['returns']) }}
        </td>
    </tr>

    <tr>
        <td>Total Items</td>
        <td class="text-right">
            {{ number_format($summary['items']) }}
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
        <td>Refund Amount</td>
        <td class="text-right">
            <strong>
                {{ number_format($summary['refund'],2) }}
            </strong>
        </td>
    </tr>

</table>
<div class="footer">

    <div>

        Report generated on

        {{ now()->format('Y-m-d H:i:s') }}

    </div>

    <div style="margin-top:6px;">

        {{ $company->company_name ?? config('app.name') }}

    </div>

    <div style="margin-top:6px;">

        *** End of Purchase Return Report ***

    </div>

</div>

<script>

window.onload = function () {

    window.print();

};

</script>

</body>

</html>