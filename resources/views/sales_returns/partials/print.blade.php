<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<title>

Sales Return {{ $salesReturn->return_no }}

</title>

<style>

body{

    font-family: DejaVu Sans, Arial, sans-serif;

    font-size:12px;

    color:#222;

    margin:20px;

}

.header{

    width:100%;

    border-bottom:2px solid #000;

    margin-bottom:15px;

    padding-bottom:10px;

}

.logo{

    width:90px;

    float:left;

}

.company{

    margin-left:110px;

}

.company h2{

    margin:0;

    font-size:22px;

}

.company p{

    margin:2px 0;

    font-size:12px;

}

.clear{

    clear:both;

}

.title{

    text-align:center;

    font-size:22px;

    font-weight:bold;

    margin:15px 0;

    letter-spacing:1px;

}

.info-table{

    width:100%;

    border-collapse:collapse;

    margin-bottom:20px;

}

.info-table td{

    padding:5px;

    vertical-align:top;

}

.items{

    width:100%;

    border-collapse:collapse;

}

.items th{

    border:1px solid #000;

    padding:8px;

    background:#f2f2f2;

    text-align:center;

}

.items td{

    border:1px solid #000;

    padding:6px;

}

.text-right{

    text-align:right;

}

.text-center{

    text-align:center;

}

.total-table{

    width:35%;

    float:right;

    border-collapse:collapse;

    margin-top:15px;

}

.total-table td{

    border:1px solid #000;

    padding:8px;

}

.signature{

    width:100%;

    margin-top:70px;

}

.signature td{

    text-align:center;

    width:33%;

}

.footer{

    margin-top:40px;

    text-align:center;

    font-size:11px;

    color:#666;

}

</style>

</head>

<body>

<div class="header">

@if($company && $company->logo)

<img

    src="{{ public_path('storage/'.$company->logo) }}"

    class="logo">

@endif

<div class="company">

<h2>

{{ $company->company_name }}

</h2>

<p>

{{ $company->address }}

</p>

<p>

Tel :

{{ $company->phone }}

@if($company->email)

|

{{ $company->email }}

@endif

</p>

@if($company->website)

<p>

{{ $company->website }}

</p>

@endif

</div>

<div class="clear"></div>

</div>

<div class="title">

SALES RETURN

</div>

<table class="info-table">

<tr>

<td width="50%">

<strong>Return No :</strong>

{{ $salesReturn->return_no }}

<br>

<strong>Return Date :</strong>

{{ $salesReturn->return_date->format('d/m/Y') }}

<br>

<strong>Original Invoice :</strong>

{{ $salesReturn->sale->invoice_no ?? $salesReturn->sale->sale_no }}

</td>

<td width="50%">

<strong>Customer :</strong>

{{ $salesReturn->sale->customer->name }}

<br>

<strong>Phone :</strong>

{{ $salesReturn->sale->customer->phone ?? '-' }}

<br>

<strong>Address :</strong>

{{ $salesReturn->sale->customer->address ?? '-' }}

</td>

</tr>

</table>
<table class="items">

    <thead>

    <tr>

        <th width="5%">#</th>
        <th>Product</th>
        <th width="10%">Qty</th>
        <th width="12%">Unit Price</th>
        <th width="12%">Subtotal</th>
        <th width="18%">Reason</th>

    </tr>

    </thead>

    <tbody>

    @foreach($salesReturn->details as $detail)

        <tr>

            <td class="text-center">

                {{ $loop->iteration }}

            </td>

            <td>

                <strong>

                    {{ $detail->saleDetail->product->product_name }}

                </strong>

                <br>

                <small>

                    {{ $detail->saleDetail->product->sku }}

                </small>

            </td>

            <td class="text-center">

                {{ number_format($detail->qty,2) }}

            </td>

            <td class="text-right">

                {{ number_format($detail->unit_price,2) }}

            </td>

            <td class="text-right">

                {{ number_format($detail->subtotal,2) }}

            </td>

            <td>

                {{ $detail->reason ?: '-' }}

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

<table class="total-table">

    <tr>

        <td>

            <strong>Return Total</strong>

        </td>

        <td class="text-right">

            <strong>

                {{ number_format($salesReturn->grand_total,2) }}

            </strong>

        </td>

    </tr>

</table>

<div style="clear:both;"></div>

@if($salesReturn->remark)

<br>

<table class="info-table">

    <tr>

        <td>

            <strong>Remark :</strong>

            {{ $salesReturn->remark }}

        </td>

    </tr>

</table>

@endif

<table class="signature">

    <tr>

        <td>

            Prepared By

        </td>

        <td>

            Customer

        </td>

        <td>

            Authorized By

        </td>

    </tr>

    <tr>

        <td style="height:80px;vertical-align:bottom;">

            ______________________

            <br>

            {{ optional($salesReturn->creator)->name ?? auth()->user()->name }}

        </td>

        <td style="vertical-align:bottom;">

            ______________________

        </td>

        <td style="vertical-align:bottom;">

            ______________________

        </td>

    </tr>

</table>

<div class="footer">

    Printed on

    {{ now()->format('d/m/Y H:i:s') }}

</div>

<script>

window.onload = function(){

    window.print();

};

</script>

</body>

</html>