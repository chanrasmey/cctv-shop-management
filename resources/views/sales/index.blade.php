@extends('adminlte::page')

@section('title', 'Sales')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fas fa-shopping-cart text-primary"></i>
        Sales
    </h1>

    <a href="{{ route('sales.create') }}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i>
        New Sale
    </a>
</div>
@stop

@section('content')

<div class="row">

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-info">

            <div class="inner">

                <h3>{{ number_format($todaySales) }}</h3>

                <p>Today's Sales</p>

            </div>

            <div class="icon">

                <i class="fas fa-calendar-day"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-success">

            <div class="inner">

                <h3>{{ number_format($monthSales) }}</h3>

                <p>This Month</p>

            </div>

            <div class="icon">

                <i class="fas fa-chart-line"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-primary">

            <div class="inner">

                <h3>{{ number_format($totalSales) }}</h3>

                <p>Total Sales</p>

            </div>

            <div class="icon">

                <i class="fas fa-shopping-cart"></i>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6">

        <div class="small-box bg-warning">

            <div class="inner">

                <h3>{{ number_format($totalRevenue,2) }}</h3>

                <p>Total Revenue</p>

            </div>

            <div class="icon">

                <i class="fas fa-dollar-sign"></i>

            </div>

        </div>

    </div>

</div>

<div class="card-header">

    <h3 class="card-title">

        <i class="fas fa-list mr-1"></i>

        Sales List

    </h3>

    <div class="card-tools">

        <a href="{{ route('sales.create') }}"
           class="btn btn-primary btn-sm">

            <i class="fas fa-plus"></i>

            New Sale

        </a>

    </div>

</div>

    <div class="table-responsive">

        <table class="table table-bordered table-hover">

            <thead class="thead-light">

                <tr>

                    <th width="60">#</th>

                    <th>Invoice</th>

                    <th>Date</th>

                    <th>Customer</th>

                    <th class="text-right">Grand Total</th>

                    <th class="text-right">Paid</th>

                    <th class="text-right">Balance</th>

                    <th>Status</th>

                    <th>Created By</th>

                    <th width="200">Action</th>

                </tr>

            </thead>

            <tbody>

@forelse($sales as $sale)

<tr>

    <td>
        {{ $sales->firstItem() + $loop->index }}
    </td>

    <td>

        <strong>{{ $sale->invoice_no }}</strong>

        <br>

        <small class="text-muted">
            {{ $sale->sale_no }}
        </small>

    </td>

    <td>

        {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}

    </td>

    <td>

        {{ $sale->customer?->name ?? 'Walk-in Customer' }}

    </td>

    <td class="text-end">

        {{ number_format($sale->grand_total,2) }}

    </td>

    <td class="text-end text-success">

        {{ number_format($sale->paid_amount,2) }}

    </td>

    <td class="text-end text-danger">

        {{ number_format($sale->balance,2) }}

    </td>

    <td>

        @if($sale->status=='Completed')

            <span class="badge badge-success">
                Completed
            </span>

        @elseif($sale->status=='Draft')

            <span class="badge badge-warning">
                Draft
            </span>

        @else

            <span class="badge badge-secondary">
                {{ $sale->status }}
            </span>

        @endif

    </td>

    <td>

        {{ $sale->creator?->name }}

    </td>

    <td>

        <div class="btn-group btn-group-sm">

            <a
                href="{{ route('sales.show',$sale) }}"
                class="btn btn-info">

                <i class="fas fa-eye"></i>

            </a>

            <a
                href="{{ route('sales.print',$sale) }}"
                class="btn btn-secondary"
                target="_blank">

                <i class="fas fa-print"></i>

            </a>

            <a
                href="{{ route('sales.edit',$sale) }}"
                class="btn btn-warning">

                <i class="fas fa-edit"></i>

            </a>

            <form
                action="{{ route('sales.destroy',$sale) }}"
                method="POST"
                style="display:inline">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-danger"
                    onclick="return confirm('Delete this sale?')">

                    <i class="fas fa-trash"></i>

                </button>

            </form>

        </div>

    </td>

</tr>

@empty

<tr>

    <td colspan="10" class="text-center">

        No sales found.

    </td>

</tr>

@endforelse

</tbody>

        </table>

    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">

        <div>

            Showing
            {{ $sales->firstItem() ?? 0 }}
            -
            {{ $sales->lastItem() ?? 0 }}
            of
            {{ $sales->total() }}
            record(s)

        </div>

        <div>

            {{ $sales->links() }}

        </div>

    </div>

</div>

@stop