<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Purchase Report</title>

    <style>

        *{
            box-sizing:border-box;
        }

        body{
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size:12px;
            color:#222;
            margin:30px;
        }

        .header{
            width:100%;
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
            margin-top:25px;
            margin-bottom:20px;
        }

        .title h3{
            margin:0;
            font-size:22px;
        }

        .filters{
            width:100%;
            border-collapse:collapse;
            margin-bottom:25px;
        }

        .filters td{
            padding:6px 10px;
            border:1px solid #ddd;
        }

        .filters td:first-child{
            width:160px;
            background:#f5f5f5;
            font-weight:bold;
        }

        table.report{
            width:100%;
            border-collapse:collapse;
        }

        table.report th{
            background:#efefef;
            border:1px solid #999;
            padding:8px;
            text-align:center;
        }

        table.report td{
            border:1px solid #ccc;
            padding:7px;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
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
            margin-top:25px;
            width:40%;
            margin-left:auto;
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

                <p>
                    Tel:
                    {{ $company->phone ?? '-' }}
                </p>

                <p>
                    Email:
                    {{ $company->email ?? '-' }}
                </p>

            </td>

        </tr>

    </table>

</div>

<div class="title">

    <h3>Purchase Report</h3>

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

        @php
            $badge = match($purchase->status){
                'Completed' => 'success',
                'Pending'   => 'warning',
                'Cancelled' => 'danger',
                default     => 'secondary',
            };
        @endphp

        <span class="badge {{ $badge }}">
            {{ $purchase->status }}
        </span>

    </td>

</tr>

@empty

<tr>

    <td colspan="8"
        class="text-center"
        style="padding:25px;">

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
            <strong>{{ number_format($summary['grand_total'],2) }}</strong>
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
        Report generated on {{ now()->format('Y-m-d H:i:s') }}
    </div>

    <div style="margin-top:6px;">
        {{ $company->company_name ?? config('app.name') }}
    </div>

    <div style="margin-top:6px;">
        *** End of Purchase Report ***
    </div>

</div>

<script>

window.onload = function () {

    window.print();

};

</script>

</body>

</html>