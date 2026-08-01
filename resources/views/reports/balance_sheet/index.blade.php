@extends('adminlte::page')

@section('title', 'Balance Sheet Report')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="mb-1">Balance Sheet Report</h1>

        <small class="text-muted">
            Financial Position Statement
        </small>

    </div>

    <div>

        <a href="{{ route('reports.balance-sheet.print', request()->query()) }}"
           target="_blank"
           class="btn btn-outline-secondary">

            <i class="fas fa-print"></i> Print

        </a>

        <a href="{{ route('reports.balance-sheet.pdf', request()->query()) }}"
           class="btn btn-danger">

            <i class="fas fa-file-pdf"></i> PDF

        </a>

        <a href="{{ route('reports.balance-sheet.excel', request()->query()) }}"
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

            Report Date

        </h3>

    </div>

    <form method="GET">

        <div class="card-body">

            <div class="row">

                <div class="col-md-5">

                    <label>As Of Date</label>

                    <input
                        type="date"
                        name="date_to"
                        class="form-control"
                        value="{{ $filters['date_to'] ?? '' }}">

                </div>

                <div class="col-md-3 d-flex align-items-end">

                    <button
                        type="submit"
                        class="btn btn-primary mr-2">

                        <i class="fas fa-search"></i>

                        Generate

                    </button>

                    <a
                        href="{{ route('reports.balance-sheet.index') }}"
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

                <h3>{{ number_format($report['total_assets'], 2) }}</h3>

                <p>Total Assets</p>

            </div>

            <div class="icon">

                <i class="fas fa-wallet"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ number_format($report['total_liabilities'], 2) }}</h3>

                <p>Total Liabilities</p>

            </div>

            <div class="icon">

                <i class="fas fa-file-invoice-dollar"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        @php
            $balanceClass = $report['balanced']
                ? 'bg-primary'
                : 'bg-danger';
        @endphp

        <div class="small-box {{ $balanceClass }}">

            <div class="inner">

                <h3>{{ number_format($report['total_equity'], 2) }}</h3>

                <p>Total Equity</p>

            </div>

            <div class="icon">

                <i class="fas fa-balance-scale"></i>

            </div>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">

        <h3 class="card-title">

            Balance Sheet Statement

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

                <tr class="table-success">

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

                <tr class="table-success">

                    <th>Total Assets</th>

                    <th class="text-right">

                        {{ number_format($report['total_assets'], 2) }}

                    </th>

                </tr>

                <tr class="table-warning">

                    <th colspan="2">

                        LIABILITIES

                    </th>

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

                <tr class="table-warning">

                    <th>Total Liabilities</th>

                    <th class="text-right">

                        {{ number_format($report['total_liabilities'], 2) }}

                    </th>

                </tr>

                <tr class="table-primary">

                    <th colspan="2">

                        OWNER'S EQUITY

                    </th>

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

                <tr class="table-primary">

                    <th>Total Equity</th>

                    <th class="text-right">

                        {{ number_format($report['total_equity'], 2) }}

                    </th>

                </tr>

                <tr class="{{ $report['balanced'] ? 'table-success' : 'table-danger' }}">

                    <th>Liabilities + Equity</th>

                    <th class="text-right">

                        {{ number_format($report['liabilities_and_equity'], 2) }}

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

                    Status :

                    @if($report['balanced'])

                        <span class="text-success">

                            ✓ Balanced

                        </span>

                    @else

                        <span class="text-danger">

                            ✗ Not Balanced

                        </span>

                    @endif

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
.table-warning th,
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