@extends('adminlte::page')

@section('title', 'Purchases')

@section('content_header')
    <h1>Purchases</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">
        &times;
    </button>

    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">
        &times;
    </button>

    {{ session('error') }}
</div>
@endif

<div class="card">

    <div class="card-header">

        <a href="{{ route('purchases.create') }}"
           class="btn btn-primary">

            <i class="fas fa-plus"></i>

            New Purchase

        </a>

        <a href="{{ route('reports.purchases') }}"
           class="btn btn-secondary">

            <i class="fas fa-file-invoice"></i>

            Purchase Report

        </a>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover">

            <thead class="table-dark">

                <tr>

                    <th width="50">#</th>

                    <th>Purchase No</th>

                    <th>Date</th>

                    <th>Supplier</th>

                    <th>Invoice No</th>

                    <th class="text-end">Grand Total</th>

                    <th class="text-end">Paid</th>

                    <th class="text-end">Balance</th>

                    <th>Status</th>

                    <th width="220">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($purchases as $purchase)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $purchase->purchase_no }}</td>

                        <td>{{ $purchase->purchase_date->format('d-m-Y') }}</td>

                        <td>{{ $purchase->supplier?->company_name }}</td>

                        <td>{{ $purchase->invoice_no }}</td>

                        <td class="text-end">
                            {{ number_format($purchase->grand_total,2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($purchase->paid_amount,2) }}
                        </td>

                        <td class="text-end">
                            {{ number_format($purchase->balance,2) }}
                        </td>

                        <td>

                            @switch($purchase->status)

                                @case('Completed')

                                    <span class="badge badge-success">
                                        Completed
                                    </span>

                                    @break

                                @case('Pending')

                                    <span class="badge badge-warning">
                                        Pending
                                    </span>

                                    @break

                                @case('Cancelled')

                                    <span class="badge badge-danger">
                                        Cancelled
                                    </span>

                                    @break

                                @default

                                    <span class="badge badge-secondary">
                                        Draft
                                    </span>

                            @endswitch

                        </td>

                        <td>

                            <a href="{{ route('purchases.show',$purchase) }}"
                               class="btn btn-info btn-sm">

                                <i class="fas fa-eye"></i>

                            </a>

                            @if($purchase->isDraft() || $purchase->isPending())
                                <a href="{{ route('purchases.edit',$purchase) }}"
                                   class="btn btn-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>
                            @endif

                            @if($purchase->isCompleted())
                                <a href="{{ route('purchases.returns.create',$purchase) }}"
                                   class="btn btn-danger btn-sm">

                                    <i class="fas fa-undo"></i>

                                </a>
                            @endif

                            @if(! $purchase->isCancelled())
                                <form action="{{ route('purchases.cancel',$purchase) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    @method('PATCH')

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Cancel this purchase? Completed purchases will reverse stock.')">

                                        <i class="fas fa-ban"></i>

                                    </button>

                                </form>
                            @endif

                            @if($purchase->isDraft())
                                <form action="{{ route('purchases.destroy',$purchase) }}"
                                      method="POST"
                                      class="d-inline">

                                    @csrf

                                    @method('DELETE')

                                    <button class="btn btn-secondary btn-sm"
                                            onclick="return confirm('Delete this draft purchase?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="10" class="text-center">

                            No purchase found.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="card-footer">

        {{ $purchases->links() }}

    </div>

</div>

@stop
