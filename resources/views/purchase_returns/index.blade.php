@extends('adminlte::page')

@section('title', 'Purchase Returns')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>

        Purchase Returns

    </h1>

</div>

@stop

@section('content')

@if(session('success'))

<div class="alert alert-success alert-dismissible">

    <button
        type="button"
        class="close"
        data-dismiss="alert">

        &times;

    </button>

    {{ session('success') }}

</div>

@endif

@if(session('error'))

<div class="alert alert-danger alert-dismissible">

    <button
        type="button"
        class="close"
        data-dismiss="alert">

        &times;

    </button>

    {{ session('error') }}

</div>

@endif

<div class="card">

    <div class="card-header">

        <form
            method="GET"
            class="row">

            <div class="col-md-4">

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Return No / Purchase No / Supplier..."
                    value="{{ request('search') }}">

            </div>

            <div class="col-md-2">

                <button
                    class="btn btn-primary">

                    <i class="fas fa-search"></i>

                    Search

                </button>

            </div>

        </form>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover mb-0">

                <thead>

                <tr>

                    <th width="50">

                        #

                    </th>

                    <th>

                        Return No

                    </th>

                    <th>

                        Date

                    </th>

                    <th>

                        Purchase No

                    </th>

                    <th>

                        Supplier

                    </th>

                    <th class="text-end">

                        Total

                    </th>

                    <th>

                        Status

                    </th>

                    <th width="160">

                        Action

                    </th>

                </tr>

                </thead>

                <tbody>

                @forelse($purchaseReturns as $purchaseReturn)

                <tr>

                    <td>

                        {{ $purchaseReturns->firstItem()+$loop->index }}

                    </td>

                    <td>

                        {{ $purchaseReturn->return_no }}

                    </td>

                    <td>

                        {{ $purchaseReturn->return_date->format('d-m-Y') }}

                    </td>

                    <td>

                        {{ $purchaseReturn->purchase?->purchase_no }}

                    </td>

                    <td>

                        {{ $purchaseReturn->supplier?->company_name }}

                    </td>

                    <td class="text-end">

                        {{ number_format($purchaseReturn->subtotal,2) }}

                    </td>

                    <td>

                        {!! $purchaseReturn->status_badge !!}

                    </td>

                    <td>
                        <a
                            href="{{ route('purchase-returns.show', $purchaseReturn) }}"
                            class="btn btn-info btn-sm"
                            title="View">

                            <i class="fas fa-eye"></i>

                        </a>

                        <a
                            href="{{ route('purchase-returns.print', $purchaseReturn) }}"
                            target="_blank"
                            class="btn btn-primary btn-sm"
                            title="Print">

                            <i class="fas fa-print"></i>

                        </a>

                        @if($purchaseReturn->isCompleted())

                            <form
                                action="{{ route('purchase-returns.cancel', $purchaseReturn) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Cancel this Purchase Return? Stock will be restored.')">

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm"
                                    title="Cancel">

                                    <i class="fas fa-ban"></i>

                                </button>

                            </form>

                        @endif

                    </td>

                </tr>

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center text-muted py-4">

                            No Purchase Returns found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>
    <div class="card-footer clearfix">

        {{ $purchaseReturns->withQueryString()->links() }}

    </div>

</div>

@stop