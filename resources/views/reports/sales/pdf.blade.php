<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <title>Sales Report</title>

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        h2,
        h3,
        p {
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
        }

        .report-title {
            font-size: 16px;
            margin-top: 5px;
            font-weight: bold;
        }

        .filters {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #efefef;
            border: 1px solid #999;
            padding: 7px;
            text-align: left;
        }

        td {
            border: 1px solid #999;
            padding: 6px;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        tfoot td {
            font-weight: bold;
            background: #f5f5f5;
        }

        .footer {
            position: fixed;
            bottom: -5px;
            left: 0;
            right: 0;
            font-size: 10px;
        }

        .footer table {
            border: none;
        }

        .footer td {
            border: none;
        }
    </style>

</head>

<body>

<div class="header">

    @if(!empty($company?->logo) && file_exists(public_path('storage/'.$company->logo)))
        <img src="{{ public_path('storage/'.$company->logo) }}"
             style="height:70px; margin-bottom:10px;">
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
        SALES REPORT
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

    <th width="18%">Invoice</th>

    <th>Customer</th>

    <th width="8%">Items</th>

    <th width="15%">Grand Total</th>

    <th width="15%">Paid</th>

    <th width="15%">Balance</th>

</tr>

</thead>

<tbody>

@forelse($sales as $sale)

<tr>

    <td class="center">
        {{ $sale->sale_date->format('Y-m-d') }}
    </td>

    <td>
        {{ $sale->invoice_no }}
    </td>

    <td>
        {{ optional($sale->customer)->name }}
    </td>

    <td class="center">
        {{ $sale->details->count() }}
    </td>

    <td class="right">
        {{ number_format($sale->grand_total,2) }}
    </td>

    <td class="right">
        {{ number_format($sale->paid_amount,2) }}
    </td>

    <td class="right">
        {{ number_format($sale->balance,2) }}
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

    <td class="right">
        {{ number_format($summary['grand_total'] ?? 0,2) }}
    </td>

    <td class="right">
        {{ number_format($summary['paid_amount'] ?? 0,2) }}
    </td>

    <td class="right">
        {{ number_format($summary['balance'] ?? 0,2) }}
    </td>

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
Page <script type="text/php">
if (isset($pdf)) {
    $x = 520;
    $y = 815;
    $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
    $font = $fontMetrics->get_font("Helvetica");
    $size = 9;
    $pdf->page_text($x, $y, $text, $font, $size);
}
</script>
</td>

</tr>

</table>

</div>

</body>

</html>