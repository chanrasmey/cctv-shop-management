<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Return Report</title>

    <style>

        body{
            font-family:DejaVu Sans,Arial,sans-serif;
            font-size:12px;
            color:#000;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,td{
            border:1px solid #000;
            padding:6px;
        }

        th{
            background:#f2f2f2;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .no-border td{
            border:none;
            padding:2px;
        }

        h2,h3{
            margin:0;
        }

        .mt-20{
            margin-top:20px;
        }

        @media print{
            .no-print{
                display:none;
            }
        }

    </style>

</head>

<body>

<table class="no-border">

<tr>

<td width="70%">

<h2>{{ $company->company_name ?? config('app.name') }}</h2>

<div>{{ $company->address ?? '' }}</div>

<div>{{ $company->phone ?? '' }}</div>

<div>{{ $company->email ?? '' }}</div>

</td>

<td class="text-right">

<h2>SALES RETURN REPORT</h2>

<strong>{{ now()->format('d-M-Y H:i') }}</strong>

</td>

</tr>

</table>

<div class="mt-20">

<table class="no-border">

<tr>

<td>

<strong>Date From :</strong>

{{ $filters['date_from'] ?? 'All' }}

</td>

<td>

<strong>Date To :</strong>

{{ $filters['date_to'] ?? 'All' }}

</td>

<td>

<strong>Status :</strong>

{{ $filters['status'] ?? 'All' }}

</td>

</tr>

</table>

</div>

<div class="mt-20">

<table>

<thead>

<tr>

<th width="10%">Date</th>

<th width="18%">Return No</th>

<th width="18%">Invoice</th>

<th>Customer</th>

<th width="8%">Items</th>

<th width="15%">Amount</th>

<th width="11%">Status</th>

</tr>

</thead>

<tbody>

@php

$totalReturn=0;
$totalQty=0;

@endphp

@foreach($returns as $return)

@php

$totalReturn += $return->grand_total;
$totalQty += $return->details->sum('qty');

@endphp

<tr>

<td>{{ $return->return_date->format('Y-m-d') }}</td>

<td>{{ $return->return_no }}</td>

<td>{{ optional($return->sale)->invoice_no }}</td>

<td>{{ optional($return->customer)->name }}</td>

<td class="text-center">{{ $return->details->sum('qty') }}</td>

<td class="text-right">{{ number_format($return->grand_total,2) }}</td>

<td class="text-center">{{ $return->status }}</td>

</tr>

@endforeach

</tbody>

<tfoot>

<tr>

<th colspan="4" class="text-right">

TOTAL

</th>

<th class="text-center">

{{ number_format($totalQty) }}

</th>

<th class="text-right">

{{ number_format($totalReturn,2) }}

</th>

<th></th>

</tr>

</tfoot>

</table>

</div>

<div class="mt-20">

<table class="no-border">

<tr>

<td width="70%"></td>

<td>

<table>

<tr>

<th>Total Return</th>

<td class="text-right">

{{ number_format($totalReturn,2) }}

</td>

</tr>

<tr>

<th>Returned Qty</th>

<td class="text-right">

{{ number_format($totalQty) }}

</td>

</tr>

</table>

</td>

</tr>

</table>

</div>

<script>

window.print();

</script>

</body>

</html>