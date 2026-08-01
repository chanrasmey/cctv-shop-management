@extends('adminlte::page')

@section('title', 'Profit & Loss Report')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="mb-1">Profit & Loss Report</h1>

        <small class="text-muted">
            Financial Performance Summary
        </small>

    </div>

    <div>

        <a href="{{ route('reports.profit-loss.print', request()->query()) }}"
           target="_blank"
           class="btn btn-outline-secondary">

            <i class="fas fa-print"></i> Print

        </a>

        <a href="{{ route('reports.profit-loss.pdf', request()->query()) }}"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i> PDF

        </a>

        <a href="{{ route('reports.profit-loss.excel', request()->query()) }}"
           class="btn btn-success">

            <i class="fas fa-file-excel"></i> Excel

        </a>

    </div>

</div>

@stop

@section('content')

<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">

            Report Period

        </h3>

    </div>

    <form method="GET">

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <label>Date From</label>

                    <input
                        type="date"
                        name="date_from"
                        class="form-control"
                        value="{{ $filters['date_from'] ?? '' }}">

                </div>

                <div class="col-md-4">

                    <label>Date To</label>

                    <input
                        type="date"
                        name="date_to"
                        class="form-control"
                        value="{{ $filters['date_to'] ?? '' }}">

                </div>

                <div class="col-md-4 d-flex align-items-end">

                    <button class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>

                        Generate

                    </button>

                    <a
                        href="{{ route('reports.profit-loss.index') }}"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </div>

        </div>

    </form>

</div>
<div class="row">

    <div class="col-lg-4">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>{{ number_format($report['net_sales'], 2) }}</h3>

                <p>Net Sales</p>

            </div>

            <div class="icon">

                <i class="fas fa-shopping-cart"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ number_format($report['gross_profit'], 2) }}</h3>

                <p>Gross Profit</p>

            </div>

            <div class="icon">

                <i class="fas fa-chart-line"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        @php
            $profitClass = $report['net_profit'] >= 0
                ? 'bg-primary'
                : 'bg-danger';
        @endphp

        <div class="small-box {{ $profitClass }}">

            <div class="inner">

                <h3>{{ number_format($report['net_profit'], 2) }}</h3>

                <p>Net Profit / Loss</p>

            </div>

            <div class="icon">

                <i class="fas fa-coins"></i>

            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Profit & Loss Statement

        </h3>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th>Description</th>

                    <th width="220" class="text-right">

                        Amount

                    </th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>Sales Revenue</td>

                    <td class="text-right">

                        {{ number_format($report['sales_revenue'], 2) }}

                    </td>

                </tr>

                <tr>

                    <td>Less: Sales Returns</td>

                    <td class="text-right text-danger">

                        ({{ number_format($report['sales_returns'], 2) }})

                    </td>

                </tr>

                <tr class="table-success">

                    <th>Net Sales</th>

                    <th class="text-right">

                        {{ number_format($report['net_sales'], 2) }}

                    </th>

                </tr>

                <tr>

                    <td>Cost of Goods Sold (COGS)</td>

                    <td class="text-right text-danger">

                        ({{ number_format($report['cost_of_goods_sold'], 2) }})

                    </td>

                </tr>

                <tr class="table-primary">

                    <th>Gross Profit</th>

                    <th class="text-right">

                        {{ number_format($report['gross_profit'], 2) }}

                    </th>

                </tr>

                <tr>

                    <td>Purchases</td>

                    <td class="text-right">

                        {{ number_format($report['purchase_amount'], 2) }}

                    </td>

                </tr>

                <tr>

                    <td>Less: Purchase Returns</td>

                    <td class="text-right text-danger">

                        ({{ number_format($report['purchase_returns'], 2) }})

                    </td>

                </tr>

                <tr>

                    <th>Net Purchases</th>

                    <th class="text-right">

                        {{ number_format($report['net_purchases'], 2) }}

                    </th>

                </tr>

                <tr class="{{ $report['net_profit'] >= 0 ? 'table-success' : 'table-danger' }}">

                    <th>Net Profit / Loss</th>

                    <th class="text-right">

                        {{ number_format($report['net_profit'], 2) }}

                    </th>

                </tr>

            </tbody>

        </table>

    </div>
        <div class="card-footer">

        <div class="row">

            <div class="col-md-6">

                <small class="text-muted">

                    Report generated at
                    {{ now()->format('Y-m-d H:i:s') }}

                </small>

            </div>

            <div class="col-md-6 text-right">

                <strong>

                    Net Result :

                    <span class="{{ $report['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">

                        {{ number_format($report['net_profit'], 2) }}

                    </span>

                </strong>

            </div>

        </div>

    </div>

</div>

@stop

@section('css')

<style>

.small-box .inner h3{
    font-size:1.8rem;
}

.table th,
.table td{
    vertical-align:middle;
}

.table-success th,
.table-primary th,
.table-danger th{
    font-size:15px;
}

.text-right{
    font-weight:600;
}

</style>

@stop

@section('js')

<script>

$(function () {

    $('form').on('submit', function () {

        const btn = $(this).find('button[type=submit]');

        btn.prop('disabled', true);

        btn.html(
            '<i class="fas fa-spinner fa-spin"></i> Generating...'
        );

    });

});

</script>

@stop