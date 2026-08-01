<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Sales Return Report</title>

<style>

@page{
    margin:25px;
}

body{
    font-family:DejaVu Sans,sans-serif;
    font-size:11px;
    color:#222;
}

h2,h3,p{
    margin:0;
}

.header{
    text-align:center;
    margin-bottom:15px;
}

.company-name{
    font-size:22px;
    font-weight:bold;
}

.report-title{
    font-size:16px;
    font-weight:bold;
    margin-top:5px;
}

.filters{
    margin-top:10px;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#efefef;
    border:1px solid #999;
    padding:7px;
    text-align:left;
}

td{
    border:1px solid #999;
    padding:6px;
}

.right{
    text-align:right;
}

.center{
    text-align:center;
}

tfoot td{
    font-weight:bold;
    background:#f5f5f5;
}

.footer{
    position:fixed;
    bottom:-5px;
    left:0;
    right:0;
    font-size:10px;
}

.footer table,
.footer td{
    border:none;
}

</style>

</head>

<body>

<div class="header">

@if(!empty($company?->logo) && file_exists(public_path('storage/'.$company->logo)))
<img src="{{ public_path('storage/'.$company->logo) }}"
     style="height:70px;margin-bottom:10px;">
@endif

<div class="company-name">

{{ $company->company_name ?? config('app.name') }}

</div>

<div>

{{ $company->address ?? '' }}

</div>

<div>

Tel : {{ $company->phone ?? '' }}

</div>

<div class="report-title">

SALES RETURN REPORT

</div>

</div>

<div class="filters">

<strong>From :</strong>

{{ $filters['date_from'] ?? 'All' }}

&nbsp;&nbsp;

<strong>To :</strong>

{{ $filters['date_to'] ?? 'All' }}

</div>

<table>

<thead>

<tr>

<th width="10%">Date</th>
<th width="16%">Return No</th>
<th width="16%">Invoice</th>
<th>Customer</th>
<th width="10%">Qty</th>
<th width="16%">Amount</th>
<th width="12%">Status</th>

</tr>

</thead>

<tbody>

@php

$totalAmount=0;
$totalQty=0;

@endphp

@forelse($returns as $return)

@php

$totalAmount += $return->grand_total;
$totalQty += $return->details->sum('qty');

@endphp

<tr>

<td class="center">

{{ $return->return_date->format('Y-m-d') }}

</td>

<td>

{{ $return->return_no }}

</td>

<td>

{{ optional($return->sale)->invoice_no }}

</td>

<td>

{{ optional($return->customer)->name }}

</td>

<td class="center">

{{ $return->details->sum('qty') }}

</td>

<td class="right">

{{ number_format($return->grand_total,2) }}

</td>

<td class="center">

{{ $return->status }}

</td>

</tr>

@empty

<tr>

<td colspan="7" class="center">

No records found.

</td>

</tr>

@endforelse

</tbody>

<tfoot>

<tr>

<td colspan="4" class="right">

TOTAL

</td>

<td class="center">

{{ number_format($totalQty) }}

</td>

<td class="right">

{{ number_format($totalAmount,2) }}

</td>

<td></td>

</tr>

</tfoot>

</table>

<div class="footer">

<table width="100%">

<tr>

<td>

Generated :

{{ $generated->format('Y-m-d H:i:s') }}

</td>

<td align="right">

Page

<script type="text/php">

if(isset($pdf)){

$x=520;
$y=815;

$text="Page {PAGE_NUM} / {PAGE_COUNT}";

$font=$fontMetrics->get_font("Helvetica");

$size=9;

$pdf->page_text($x,$y,$text,$font,$size);

}

</script>

</td>

</tr>

</table>

</div>

</body>

</html>