@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content_body')

<div class="container-fluid">

    {{-- Company Header --}}
    <div class="row mb-3">

        <div class="col-12">

            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div class="d-flex align-items-center">

                            @if(!empty($company?->logo))

                                <img src="{{ asset('storage/'.$company->logo) }}"
                                     alt="{{ $company->company_name }}"
                                     class="img-circle elevation-2 mr-3"
                                     style="width:70px;height:70px;object-fit:cover;">

                            @endif

                            <div>

                                <h3 class="mb-0 font-weight-bold">
                                    {{ $company->company_name ?? config('app.name') }}
                                </h3>

                                <small class="text-muted">
                                    {{ $company->address ?? '' }}
                                </small>

                            </div>

                        </div>

                        <div class="text-right">

                            <h5 class="mb-1">
                                {{ now()->format('l') }}
                            </h5>

                            <div class="text-muted">
                                {{ now()->format('d M Y H:i') }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Financial KPI --}}
    <div class="row">
            {{-- Today's Sales --}}
        <div class="col-lg-3 col-md-6 col-sm-6">

            <div class="small-box bg-primary">

                <div class="inner">

                    <h3>${{ number_format($todaySales, 2) }}</h3>

                    <p>Today's Sales</p>

                </div>

                <div class="icon">
                    <i class="fas fa-cash-register"></i>
                </div>

            </div>

        </div>

        {{-- Month Sales --}}
        <div class="col-lg-3 col-md-6 col-sm-6">

            <div class="small-box bg-success">

                <div class="inner">

                    <h3>${{ number_format($monthSales, 2) }}</h3>

                    <p>This Month Sales</p>

                </div>

                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>

            </div>

        </div>

        {{-- Today's Profit --}}
        <div class="col-lg-3 col-md-6 col-sm-6">

            <div class="small-box bg-warning">

                <div class="inner">

                    <h3>${{ number_format($todayProfit, 2) }}</h3>

                    <p>Today's Profit</p>

                </div>

                <div class="icon">
                    <i class="fas fa-coins"></i>
                </div>

            </div>

        </div>

        {{-- Month Profit --}}
        <div class="col-lg-3 col-md-6 col-sm-6">

            <div class="small-box bg-danger">

                <div class="inner">

                    <h3>${{ number_format($monthProfit, 2) }}</h3>

                    <p>This Month Profit</p>

                </div>

                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>

            </div>

        </div>

    </div>

    {{-- Financial Summary --}}
    <div class="row mt-3">
        {{-- Operational Statistics --}}
    <div class="row mb-4">

        <div class="col-lg-2 col-md-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $products }}</h3>
                    <p>Products</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $categories }}</h3>
                    <p>Categories</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $brands }}</h3>
                    <p>Brands</p>
                </div>
                <div class="icon">
                    <i class="fas fa-copyright"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $customers }}</h3>
                    <p>Customers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $suppliers }}</h3>
                    <p>Suppliers</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Financial Summary Cards --}}
    <div class="row">
        <div class="col-lg-3 col-md-6">

        <div class="info-box">

            <span class="info-box-icon bg-primary">
                <i class="fas fa-warehouse"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Inventory Value
                </span>

                <span class="info-box-number">
                    ${{ number_format($inventoryValue, 2) }}
                </span>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="info-box">

            <span class="info-box-icon bg-success">
                <i class="fas fa-file-invoice-dollar"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Accounts Receivable
                </span>

                <span class="info-box-number">
                    ${{ number_format($summary['accounts_receivable'] ?? 0, 2) }}
                </span>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="info-box">

            <span class="info-box-icon bg-warning">
                <i class="fas fa-hand-holding-usd"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Accounts Payable
                </span>

                <span class="info-box-number">
                    ${{ number_format($summary['accounts_payable'] ?? 0, 2) }}
                </span>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="info-box">

            <span class="info-box-icon bg-danger">
                <i class="fas fa-chart-line"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Net Profit
                </span>

                <span class="info-box-number">
                    ${{ number_format($summary['net_profit'] ?? 0, 2) }}
                </span>

            </div>

        </div>

    </div>

</div>

{{-- Low Stock & Recent Sales --}}
<div class="row">
    {{-- Low Stock Products --}}
    <div class="col-lg-6">

        <div class="card card-warning">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Low Stock Products
                </h3>
            </div>

            <div class="card-body table-responsive p-0">

                <table class="table table-hover table-sm">

                    <thead>

                        <tr>
                            <th>SKU</th>
                            <th>Product</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Minimum</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($lowStockProducts as $product)

                        <tr>

                            <td>{{ $product->sku }}</td>

                            <td>{{ $product->product_name }}</td>

                            <td class="text-center">
                                <span class="badge badge-danger">
                                    {{ $product->stock }}
                                </span>
                            </td>

                            <td class="text-center">
                                {{ $product->minimum_stock }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                No low stock products.
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- Recent Sales --}}
    <div class="col-lg-6">

        <div class="card card-success">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Recent Sales
                </h3>

            </div>

            <div class="card-body table-responsive p-0">

                <table class="table table-hover table-sm">

                    <thead>

                        <tr>

                            <th>Date</th>

                            <th>Invoice</th>

                            <th>Customer</th>

                            <th class="text-right">Amount</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($recentSales as $sale)

                        <tr>

                            <td>{{ optional($sale->sale_date)->format('d/m/Y') }}</td>

                            <td>{{ $sale->invoice_no }}</td>

                            <td>{{ $sale->customer->name ?? '-' }}</td>

                            <td class="text-right">
                                ${{ number_format($sale->grand_total, 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center text-muted">
                                No recent sales found.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

{{-- Top Selling Products --}}
<div class="row">
    <div class="col-lg-12">

        <div class="card card-primary">

            <div class="card-header">

                <h3 class="card-title">
                    <i class="fas fa-trophy mr-2"></i>
                    Top Selling Products
                </h3>

            </div>

            <div class="card-body table-responsive p-0">

                <table class="table table-hover table-striped">

                    <thead>

                        <tr>

                            <th>SKU</th>

                            <th>Product</th>

                            <th>Category</th>

                            <th>Brand</th>

                            <th class="text-center">Stock</th>

                            <th class="text-right">Sold Qty</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($topSellingProducts as $product)

                        <tr>

                            <td>{{ $product->sku }}</td>

                            <td>{{ $product->product_name }}</td>

                            <td>{{ $product->category->name ?? '-' }}</td>

                            <td>{{ $product->brand->name ?? '-' }}</td>

                            <td class="text-center">

                                @if($product->isLowStock())

                                    <span class="badge badge-danger">
                                        {{ $product->stock }}
                                    </span>

                                @else

                                    <span class="badge badge-success">
                                        {{ $product->stock }}
                                    </span>

                                @endif

                            </td>

                            <td class="text-right">
                                {{ number_format($product->sale_details_sum_qty ?? 0, 2) }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center text-muted">

                                No sales data available.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</div>

@endsection