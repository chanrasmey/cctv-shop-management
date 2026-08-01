<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        Invoice {{ $sale->invoice_no }}
    </title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <style>

        *{
            box-sizing:border-box;
        }

        html,
        body{

            margin:0;
            padding:0;
            background:#ffffff;
            color:#222;

            font-family:
                "Segoe UI",
                Arial,
                Helvetica,
                sans-serif;

            font-size:13px;
            line-height:1.45;

        }

        .invoice{

            width:210mm;
            min-height:297mm;

            margin:auto;

            padding:18mm;

            background:#fff;

        }

        table{

            width:100%;
            border-collapse:collapse;

        }

        .header-table td{

            vertical-align:top;

        }

        .company-logo{

            width:90px;
            height:90px;

            object-fit:contain;

            border:1px solid #ddd;

            padding:4px;

        }

        .company-name{

            font-size:28px;

            font-weight:700;

            color:#111;

            margin-bottom:6px;

        }

        .company-info{

            font-size:12px;

            color:#555;

            line-height:1.7;

        }

        .invoice-title{

            font-size:32px;

            font-weight:bold;

            text-align:right;

            letter-spacing:2px;

        }

        .invoice-info{

            width:100%;
            margin-top:12px;
        }

        .invoice-info th{

            width:42%;

            text-align:left;

            padding:4px 0;

            font-weight:600;

        }

        .invoice-info td{

            text-align:right;

            padding:4px 0;

        }

        hr{

            border:none;

            border-top:2px solid #333;

            margin:22px 0;

        }

        .section-title{

            font-weight:700;

            margin-bottom:8px;

            text-transform:uppercase;

            letter-spacing:1px;

        }

        .customer-box{

            border:1px solid #ddd;

            padding:14px;

            border-radius:6px;

            min-height:120px;

        }

        .customer-box strong{

            font-size:15px;

        }

        .items{

            margin-top:25px;

        }

        .items thead th{

            background:#343a40;

            color:#fff;

            padding:10px;

            border:1px solid #333;

            text-align:center;

            font-size:12px;

        }

        .items tbody td{

            border:1px solid #ddd;

            padding:8px;

        }

        .text-center{

            text-align:center;

        }

        .text-right{

            text-align:right;

        }

        .font-bold{

            font-weight:bold;

        }

        .totals{

            width:360px;

            margin-left:auto;

            margin-top:25px;

        }

        .totals th{

            border:1px solid #ddd;

            padding:8px;

            background:#f8f9fa;

        }

        .totals td{

            border:1px solid #ddd;

            padding:8px;

            text-align:right;

        }

        .grand{

            font-size:15px;

            font-weight:bold;

            background:#f2f2f2;

        }

        .footer-note{

            margin-top:30px;

            padding:12px;

            border:1px dashed #bbb;

            font-size:12px;

            color:#555;

        }

        .signature{

            margin-top:70px;

        }

        .signature td{

            width:50%;

            text-align:center;

            vertical-align:bottom;

        }

        .signature-line{

            width:220px;

            border-top:1px solid #000;

            margin:60px auto 8px;

        }

        .print-footer{

            margin-top:25px;

            text-align:center;

            color:#666;

            font-size:11px;

        }

        .no-print{

            text-align:center;

            margin-top:30px;

        }

        .btn{

            display:inline-block;

            padding:10px 20px;

            border:none;

            background:#0d6efd;

            color:#fff;

            text-decoration:none;

            border-radius:4px;

            margin:0 4px;

        }

        .btn-secondary{

            background:#6c757d;

        }

        @page{

            size:A4;

            margin:12mm;

        }

        @media print{

            .no-print{

                display:none;

            }

            body{

                background:#fff;

            }

            .invoice{

                width:100%;

                padding:0;

            }

        }

    </style>

</head>
<body>

@php
    $company = $company ?? \App\Models\CompanySetting::current();

    $companyName = $company->company_name ?? config('app.name');
    $logo = $company->logo_url;

    $address = $company->address;
    $phone = $company->phone;
    $mobile = $company->mobile;
    $email = $company->email;
    $website = $company->website;

    $invoiceFooter = $company->invoice_footer;
    $warranty = $company->warranty_policy;
@endphp

<div class="invoice">

    <table class="header-table">

        <tr>

            <td width="60%">

                <table>

                    <tr>

                        <td width="110">

                            @if($logo)

                                <img
                                    src="{{ $logo }}"
                                    class="company-logo"
                                    alt="{{ $companyName }}">

                            @endif

                        </td>

                        <td>

                            <div class="company-name">

                                {{ $companyName }}

                            </div>

                            <div class="company-info">

                                @if($address)
                                    {{ $address }}<br>
                                @endif

                                @if($phone)
                                    Phone : {{ $phone }}<br>
                                @endif

                                @if($mobile)
                                    Mobile : {{ $mobile }}<br>
                                @endif

                                @if($email)
                                    Email : {{ $email }}<br>
                                @endif

                                @if($website)
                                    Website : {{ $website }}
                                @endif

                            </div>

                        </td>

                    </tr>

                </table>

            </td>

            <td width="40%">

                <div class="invoice-title">

                    INVOICE

                </div>

                <table class="invoice-info">

                    <tr>

                        <th>Invoice No</th>

                        <td>

                            {{ $sale->invoice_no }}

                        </td>

                    </tr>

                    <tr>

                        <th>Sale No</th>

                        <td>

                            {{ $sale->sale_no }}

                        </td>

                    </tr>

                    <tr>

                        <th>Date</th>

                        <td>

                            {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}

                        </td>

                    </tr>

                    <tr>

                        <th>Status</th>

                        <td>

                            {{ ucfirst($sale->status) }}

                        </td>

                    </tr>

                    <tr>

                        <th>Prepared By</th>

                        <td>

                            {{ $sale->creator?->name }}

                        </td>

                    </tr>

                </table>

            </td>

        </tr>

    </table>

    <hr>

    <table>

        <tr>

            <td width="55%">

                <div class="section-title">

                    Bill To

                </div>

                <div class="customer-box">

                    <strong>

                        {{ $sale->customer?->name ?? 'Walk-in Customer' }}

                    </strong>

                    <br><br>

                    @if($sale->customer?->address)

                        {{ $sale->customer->address }}

                        <br>

                    @endif

                    @if($sale->customer?->phone)

                        Phone :

                        {{ $sale->customer->phone }}

                        <br>

                    @endif

                    @if($sale->customer?->email)

                        Email :

                        {{ $sale->customer->email }}

                    @endif

                </div>

            </td>

            <td width="5%"></td>

            <td width="40%">

                <div class="section-title">

                    Payment Information

                </div>

                <div class="customer-box">

                    <table>

                        <tr>

                            <td>Subtotal</td>

                            <td class="text-right">

                                {{ number_format($sale->subtotal,2) }}

                            </td>

                        </tr>

                        <tr>

                            <td>Discount</td>

                            <td class="text-right">

                                {{ number_format($sale->discount_amount,2) }}

                            </td>

                        </tr>

                        <tr>

                            <td>Tax</td>

                            <td class="text-right">

                                {{ number_format($sale->tax_amount,2) }}

                            </td>

                        </tr>

                        <tr>

                            <td class="font-bold">

                                Grand Total

                            </td>

                            <td class="text-right font-bold">

                                {{ number_format($sale->grand_total,2) }}

                            </td>

                        </tr>

                    </table>

                </div>

            </td>

        </tr>

    </table>

    <table class="items">

        <thead>

            <tr>

                <th width="5%">#</th>

                <th width="14%">SKU</th>

                <th>Product</th>

                <th width="8%">Qty</th>

                <th width="13%">Unit Price</th>

                <th width="10%">Disc%</th>

                <th width="15%">Amount</th>

            </tr>

        </thead>

        <tbody>

            @foreach($sale->details as $detail)
<tr>

    <td class="text-center">
        {{ $loop->iteration }}
    </td>

    <td>
        {{ $detail->product->sku }}
    </td>

    <td>
        <strong>{{ $detail->product->product_name }}</strong>
    </td>

    <td class="text-center">
        {{ number_format($detail->qty,0) }}
    </td>

    <td class="text-right">
        {{ number_format($detail->unit_price,2) }}
    </td>

    <td class="text-center">
        {{ number_format($detail->discount_percent,2) }}%
    </td>

    <td class="text-right">
        {{ number_format($detail->subtotal,2) }}
    </td>

</tr>

@endforeach

</tbody>

</table>

@if($sale->remark)

<div class="footer-note">

    <strong>Remark</strong>

    <br><br>

    {!! nl2br(e($sale->remark)) !!}

</div>

@endif

<table class="totals">

    <tr>

        <th>Subtotal</th>

        <td>

            {{ number_format($sale->subtotal,2) }}

        </td>

    </tr>

    <tr>

        <th>Discount</th>

        <td>

            {{ number_format($sale->discount_amount,2) }}

        </td>

    </tr>

    <tr>

        <th>Tax</th>

        <td>

            {{ number_format($sale->tax_amount,2) }}

        </td>

    </tr>

    <tr class="grand">

        <th>Grand Total</th>

        <td>

            {{ number_format($sale->grand_total,2) }}

        </td>

    </tr>

    <tr>

        <th>Paid</th>

        <td>

            {{ number_format($sale->paid_amount,2) }}

        </td>

    </tr>

    <tr>

        <th>Balance</th>

        <td>

            {{ number_format($sale->balance,2) }}

        </td>

    </tr>

    <tr>

        <th>Change</th>

        <td>

            {{ number_format($sale->change_amount,2) }}

        </td>

    </tr>

</table>

@if($warranty)

<div class="footer-note">

    <strong>Warranty Policy</strong>

    <br><br>

    {!! nl2br(e($warranty)) !!}

</div>

@endif

@if($invoiceFooter)

<div class="footer-note">

    {!! nl2br(e($invoiceFooter)) !!}

</div>

@endif

<table class="signature">

    <tr>

        <td>

            <div class="signature-line"></div>

            <strong>Customer Signature</strong>

        </td>

        <td>

            <div class="signature-line"></div>

            <strong>Authorized Signature</strong>

            <br>

            {{ $sale->creator?->name }}

        </td>

    </tr>

</table>

<div class="print-footer">

    Printed :

    {{ now()->format('d M Y H:i') }}

    @if($companyName)

        &nbsp; | &nbsp;

        {{ $companyName }}

    @endif

</div>
<div class="no-print">

    <button
        type="button"
        class="btn"
        onclick="window.print()">

        🖨 Print Invoice

    </button>

    <button
        type="button"
        class="btn btn-secondary"
        onclick="window.close()">

        ✖ Close

    </button>

</div>

</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    // Automatically open the print dialog shortly after loading.
    setTimeout(function () {
        window.print();
    }, 500);

});

// Keep the existing browser behavior after printing.
// Browsers that block window.close() on normal tabs will simply ignore it.
window.onafterprint = function () {

    try {
        window.close();
    } catch (e) {
        // Ignore if the browser prevents closing the window.
    }

};

</script>

</body>

</html>
