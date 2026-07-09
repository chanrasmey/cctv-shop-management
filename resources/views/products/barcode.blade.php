<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Barcode Label</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{

            font-family:Arial, Helvetica, sans-serif;
            background:#f4f4f4;
            padding:30px;

        }

        .label{

            width:420px;
            margin:auto;
            background:#fff;
            border:2px solid #000;
            border-radius:8px;
            padding:20px;

            text-align:center;

        }

        .company{

            font-size:22px;
            font-weight:bold;
            margin-bottom:15px;

        }

        .product{

            font-size:24px;
            font-weight:bold;
            margin-bottom:20px;
            text-transform:uppercase;

        }

        .barcode{
        text-align:center;
         margin:20px 0;
        }

        .barcode-wrapper{
         display:flex;
        justify-content:center;
        align-items:center;
        }

        .barcode-wrapper div,
        .barcode-wrapper table{
        margin:auto !important;
        }

        .barcode-number{

            font-size:22px;
            letter-spacing:2px;
            margin-top:5px;
        }

        table{

            width:100%;
            margin-top:20px;
            border-collapse:collapse;

        }

        td{

            padding:6px;
            font-size:18px;

        }

        td:first-child{

            text-align:left;
            font-weight:bold;
            width:40%;

        }

        td:last-child{

            text-align:right;

        }

        .price{

            font-size:30px;
            font-weight:bold;
            color:#000;
        }

        .footer{

            margin-top:25px;
            font-size:14px;
            color:#666;

        }

        .buttons{

            text-align:center;
            margin-top:25px;

        }

        .buttons button{

            padding:10px 25px;
            margin:5px;
            font-size:16px;
            cursor:pointer;

        }

        @media print{

            body{

                background:#fff;
                padding:0;

            }

            .buttons{

                display:none;

            }

            .label{

                border:none;
                box-shadow:none;
                width:100%;

            }

        }

    </style>

</head>

<body>

<div class="label">

    <div class="company">
        CCTV SHOP MANAGEMENT
    </div>

    <div class="product">
        {{ $product->product_name }}
    </div>

    <div class="barcode text-center">

    <div class="barcode-wrapper">

        <img
    src="data:image/png;base64,{{ DNS1D::getBarcodePNG($product->barcode, 'C128') }}"
    alt="Barcode"
    style="width:320px;height:80px;">

    </div>

    <div class="barcode-number">
        {{ $product->barcode }}
    </div>

</div>

    <table>

        <tr>
            <td>SKU</td>
            <td>{{ $product->sku }}</td>
        </tr>

        <tr>
            <td>Category</td>
            <td>{{ $product->category->name }}</td>
        </tr>

        <tr>
            <td>Brand</td>
            <td>{{ $product->brand->name }}</td>
        </tr>

        <tr>
            <td>Price</td>
            <td class="price">${{ number_format($product->sell_price,2) }}</td>
        </tr>

    </table>

    <div class="footer">

        Printed :
        {{ now()->format('d M Y H:i') }}

    </div>

</div>

<div class="buttons">

    <button onclick="window.print()">

        🖨 Print Barcode

    </button>

    <button onclick="window.close()">

        ✖ Close

    </button>

</div>

</body>

</html>