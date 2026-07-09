@extends('adminlte::page')

@section('title', 'Purchase Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center no-print">
        <h1>Purchase Report</h1>

        <button type="button" class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print"></i>
            Print
        </button>
    </div>
@stop

@section('content')

<div class="card no-print">
    <div class="card-header bg-primary">
        <h3 class="card-title">
            <i class="fas fa-filter"></i>
            Filters
        </h3>
    </div>

    <form action="{{ route('reports.purchases') }}" method="GET">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>From Date</label>
                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="{{ $filters['date_from'] }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>To Date</label>
                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="{{ $filters['date_to'] }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control">
                            <option value="">All Suppliers</option>

                            @foreach($suppliers as $supplier)
                                <option
                                    value="{{ $supplier->id }}"
                                    {{ (string) $filters['supplier_id'] === (string) $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>

                            @foreach($statusOptions as $status)
                                <option
                                    value="{{ $status }}"
                                    {{ $filters['status'] === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                Apply Filter
            </button>

            <a href="{{ route('reports.purchases') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($summary['purchase_count']) }}</h3>
                <p>Purchases</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($summary['grand_total'], 2) }}</h3>
                <p>Gross Total</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($summary['return_total'], 2) }}</h3>
                <p>Return Total</p>
            </div>
            <div class="icon">
                <i class="fas fa-undo"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ number_format($summary['net_total'], 2) }}</h3>
                <p>Net Purchase</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-secondary">
                <i class="fas fa-receipt"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Subtotal</span>
                <span class="info-box-number">{{ number_format($summary['subtotal'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning">
                <i class="fas fa-percent"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Discount</span>
                <span class="info-box-number">{{ number_format($summary['discount_amount'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info">
                <i class="fas fa-money-check-alt"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Paid</span>
                <span class="info-box-number">{{ number_format($summary['paid_amount'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-danger">
                <i class="fas fa-balance-scale"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Balance</span>
                <span class="info-box-number">{{ number_format($summary['balance'], 2) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-dark">
        <h3 class="card-title">
            <i class="fas fa-table"></i>
            Purchase Details
        </h3>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Purchase No</th>
                    <th>Date</th>
                    <th>Supplier</th>
                    <th>Invoice No</th>
                    <th>Status</th>
                    <th class="text-end">Gross</th>
                    <th class="text-end">Returns</th>
                    <th class="text-end">Net</th>
                    <th class="text-end">Paid</th>
                    <th class="text-end">Balance</th>
                </tr>
            </thead>

            <tbody>
                @forelse($purchases as $purchase)
                    @php
                        $returnTotal = (float) $purchase->returns->sum('subtotal');
                        $netTotal = (float) $purchase->grand_total - $returnTotal;
                    @endphp

                    <tr>
                        <td>{{ $purchases->firstItem() + $loop->index }}</td>
                        <td>
                            <a href="{{ route('purchases.show', $purchase) }}">
                                {{ $purchase->purchase_no }}
                            </a>
                        </td>
                        <td>{{ $purchase->purchase_date->format('d-m-Y') }}</td>
                        <td>{{ $purchase->supplier?->company_name }}</td>
                        <td>{{ $purchase->invoice_no ?: '-' }}</td>
                        <td>
                            @switch($purchase->status)
                                @case('Completed')
                                    <span class="badge badge-success">Completed</span>
                                    @break
                                @case('Pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @break
                                @case('Cancelled')
                                    <span class="badge badge-danger">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">Draft</span>
                            @endswitch
                        </td>
                        <td class="text-end">{{ number_format($purchase->grand_total, 2) }}</td>
                        <td class="text-end">{{ number_format($returnTotal, 2) }}</td>
                        <td class="text-end">{{ number_format($netTotal, 2) }}</td>
                        <td class="text-end">{{ number_format($purchase->paid_amount, 2) }}</td>
                        <td class="text-end">{{ number_format($purchase->balance, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center">No purchases found.</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr class="table-primary">
                    <th colspan="6" class="text-end">Report Total</th>
                    <th class="text-end">{{ number_format($summary['grand_total'], 2) }}</th>
                    <th class="text-end">{{ number_format($summary['return_total'], 2) }}</th>
                    <th class="text-end">{{ number_format($summary['net_total'], 2) }}</th>
                    <th class="text-end">{{ number_format($summary['paid_amount'], 2) }}</th>
                    <th class="text-end">{{ number_format($summary['balance'], 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="card-footer no-print">
        {{ $purchases->links() }}
    </div>
</div>

@stop

@section('css')
<style>
    @media print {
        .no-print,
        .main-sidebar,
        .main-header,
        .content-header {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
        }

        .card {
            box-shadow: none !important;
        }
    }
</style>
@stop
