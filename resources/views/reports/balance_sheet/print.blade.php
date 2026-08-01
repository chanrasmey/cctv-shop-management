<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <title>Balance Sheet Report</title>

    <style>

        body{
            font-family: DejaVu Sans, sans-serif;
            font-size:12px;
            color:#222;
        }

        h2,h3,p{
            margin:0;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:15px;
        }

        table th,
        table td{
            border:1px solid #444;
            padding:8px;
        }

        table th{
            background:#f3f3f3;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .header{
            margin-bottom:20px;
        }

        .company-logo{
            max-height:70px;
            margin-bottom:10px;
        }

        .section-assets th{
            background:#d4edda;
        }

        .section-liabilities th{
            background:#fff3cd;
        }

        .section-equity th{
            background:#cfe2ff;
        }

    </style>

</head>

<body>

<div class="header text-center">

    @if(!empty($company?->logo))

        <img
            src="{{ public_path('storage/'.$company->logo) }}"
            class="company-logo">

    @endif

    <h2>{{ $company->company_name ?? config('app.name') }}</h2>

    <p>{{ $company->address ?? '' }}</p>

    <p>{{ $company->phone ?? '' }}</p>

    <p>{{ $company->email ?? '' }}</p>

    <br>

    <h3>Balance Sheet Report</h3>

    <p>

        As Of Date :

        {{ $filters['date_to'] ?? now()->toDateString() }}

    </p>

</div>

<table>

    <thead>

        <tr>

            <th>Description</th>

            <th width="220" class="text-right">

                Amount

            </th>

        </tr>

    </thead>

    <tbody>
            <tr class="section-assets">
            <th colspan="2">ASSETS</th>
        </tr>

        <tr>
            <td>Cash</td>
            <td class="text-right">
                {{ number_format($report['cash'], 2) }}
            </td>
        </tr>

        <tr>
            <td>Inventory</td>
            <td class="text-right">
                {{ number_format($report['inventory'], 2) }}
            </td>
        </tr>

        <tr>
            <td>Accounts Receivable</td>
            <td class="text-right">
                {{ number_format($report['accounts_receivable'], 2) }}
            </td>
        </tr>

        <tr>
            <td>Other Assets</td>
            <td class="text-right">
                {{ number_format($report['other_assets'], 2) }}
            </td>
        </tr>

        <tr>
            <th>Total Assets</th>
            <th class="text-right">
                {{ number_format($report['total_assets'], 2) }}
            </th>
        </tr>

        <tr class="section-liabilities">
            <th colspan="2">LIABILITIES</th>
        </tr>

        <tr>
            <td>Accounts Payable</td>
            <td class="text-right">
                {{ number_format($report['accounts_payable'], 2) }}
            </td>
        </tr>

        <tr>
            <td>Other Liabilities</td>
            <td class="text-right">
                {{ number_format($report['other_liabilities'], 2) }}
            </td>
        </tr>

        <tr>
            <th>Total Liabilities</th>
            <th class="text-right">
                {{ number_format($report['total_liabilities'], 2) }}
            </th>
        </tr>

        <tr class="section-equity">
            <th colspan="2">OWNER'S EQUITY</th>
        </tr>

        <tr>
            <td>Owner Capital</td>
            <td class="text-right">
                {{ number_format($report['owner_capital'], 2) }}
            </td>
        </tr>

        <tr>
            <td>Retained Earnings</td>
            <td class="text-right">
                {{ number_format($report['retained_earnings'], 2) }}
            </td>
        </tr>

        <tr>
            <td>Current Profit</td>
            <td class="text-right">
                {{ number_format($report['current_profit'], 2) }}
            </td>
        </tr>

        <tr>
            <th>Total Equity</th>
            <th class="text-right">
                {{ number_format($report['total_equity'], 2) }}
            </th>
        </tr>

        <tr>
            <th>Liabilities + Equity</th>
            <th class="text-right">
                {{ number_format($report['liabilities_and_equity'], 2) }}
            </th>
        </tr>

        <tr>
            <th>Status</th>
            <th class="text-right">
                {{ $report['balanced'] ? 'BALANCED' : 'NOT BALANCED' }}
            </th>
        </tr>

    </tbody>

</table>

<br><br><br>

<table style="border:none;margin-top:40px;">

    <tr style="border:none;">

        <td style="border:none;width:50%;text-align:center;">

            __________________________

            <br><br>

            Prepared By

        </td>

        <td style="border:none;width:50%;text-align:center;">

            __________________________

            <br><br>

            Approved By

        </td>

    </tr>

</table>
<div style="margin-top:40px;">

    <table style="border:none;">

        <tr style="border:none;">

            <td style="border:none;text-align:left;">

                <small>

                    Generated by:
                    {{ auth()->user()->name ?? 'System' }}

                </small>

            </td>

            <td style="border:none;text-align:right;">

                <small>

                    Printed:
                    {{ now()->format('Y-m-d H:i:s') }}

                </small>

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