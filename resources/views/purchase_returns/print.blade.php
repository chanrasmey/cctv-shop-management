<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>

Purchase Return {{ $purchaseReturn->return_no }}

</title>

<style>

body{

    font-family: DejaVu Sans, sans-serif;

    font-size:12px;

    color:#222;

}

.container{

    width:100%;

}

.header{

    border-bottom:2px solid #000;

    padding-bottom:10px;

    margin-bottom:20px;

}

.company{

    float:left;

    width:60%;

}

.document{

    float:right;

    width:40%;

    text-align:right;

}

.clear{

    clear:both;

}

table{

    width:100%;

    border-collapse:collapse;

}

th{

    background:#f2f2f2;

}

th,

td{

    border:1px solid #444;

    padding:6px;

}

.text-end{

    text-align:right;

}

.text-center{

    text-align:center;

}

.info-table td{

    border:none;

    padding:3px;

}

.summary{

    margin-top:20px;

    width:35%;

    float:right;

}

.signature{

    margin-top:80px;

    width:100%;

}

.signature td{

    border:none;

    text-align:center;

}

</style>

</head>

<body>

<div class="container">

<div class="header">

<div class="company">

<h2>

{{ settings('company_name') }}

</h2>

<div>

{{ settings('company_address') }}

</div>

<div>

Tel:

{{ settings('company_phone') }}

</div>

<div>

Email:

{{ settings('company_email') }}

</div>

</div>

<div class="document">

<h2>

PURCHASE RETURN

</h2>

<strong>

{{ $purchaseReturn->return_no }}

</strong>

</div>

<div class="clear"></div>

</div>

<table class="info-table">

<tr>

<td width="20%">

Return Date

</td>

<td width="30%">

{{ $purchaseReturn->return_date->format('d-m-Y') }}

</td>

<td width="20%">

Purchase No

</td>

<td>

{{ $purchaseReturn->purchase->purchase_no }}

</td>

</tr>

<tr>

<td>

Supplier

</td>

<td>

{{ $purchaseReturn->supplier->company_name }}

</td>

<td>

Status

</td>

<td>

{{ $purchaseReturn->status }}

</td>

</tr>

</table>

<br>
<table>

    <thead>

        <tr>

            <th width="50">

                #

            </th>

            <th>

                SKU

            </th>

            <th>

                Product

            </th>

            <th width="90">

                Qty

            </th>

            <th width="120">

                Unit Cost

            </th>

            <th width="140">

                Subtotal

            </th>

        </tr>

    </thead>

    <tbody>

    @foreach($purchaseReturn->details as $detail)

        <tr>

            <td class="text-center">

                {{ $loop->iteration }}

            </td>

            <td>

                {{ $detail->product?->sku }}

            </td>

            <td>

                {{ $detail->product?->product_name }}

            </td>

            <td class="text-end">

                {{ number_format($detail->qty,2) }}

            </td>

            <td class="text-end">

                {{ number_format($detail->unit_cost,2) }}

            </td>

            <td class="text-end">

                {{ number_format($detail->subtotal,2) }}

            </td>

        </tr>

    @endforeach

    </tbody>

</table>
<div class="summary">

    <table>

        <tr>

            <th width="50%">

                Return Total

            </th>

            <td class="text-end">

                {{ number_format($purchaseReturn->subtotal, 2) }}

            </td>

        </tr>

    </table>

</div>

<div class="clear"></div>

@if(!empty($purchaseReturn->remark))

<br><br>

<strong>

Remark

</strong>

<p>

    {{ $purchaseReturn->remark }}

</p>

@endif

<br><br><br>

<table class="signature">

    <tr>

        <td width="33%">

            ______________________

            <br><br>

            Prepared By

            <br>

            {{ $purchaseReturn->user?->name ?? '' }}

        </td>

        <td width="33%">

            ______________________

            <br><br>

            Checked By

        </td>

        <td width="33%">

            ______________________

            <br><br>

            Approved By

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