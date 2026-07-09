@extends('adminlte::page')

@section('title', 'Create Purchase Return')

@section('content_header')
    <h1>Return Purchase {{ $purchase->purchase_no }}</h1>
@stop

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">
            &times;
        </button>

        {{ session('error') }}
    </div>
@endif

<form action="{{ route('purchases.returns.store', $purchase) }}" method="POST">
    @csrf

    <div class="card">
        <div class="card-header bg-primary">
            <h3 class="card-title">Return Information</h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Purchase No</label>
                        <input type="text" class="form-control" value="{{ $purchase->purchase_no }}" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Supplier</label>
                        <input type="text" class="form-control" value="{{ $purchase->supplier?->company_name }}" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Purchase Date</label>
                        <input type="text" class="form-control" value="{{ $purchase->purchase_date->format('d-m-Y') }}" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Return Date <span class="text-danger">*</span></label>
                        <input
                            type="date"
                            name="return_date"
                            class="form-control"
                            value="{{ old('return_date', now()->format('Y-m-d')) }}"
                            required>

                        @error('return_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Remark</label>
                <textarea
                    name="remark"
                    rows="3"
                    class="form-control"
                    placeholder="Optional return remark...">{{ old('remark') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-warning">
            <h3 class="card-title">Return Items</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>SKU</th>
                        <th>Product</th>
                        <th class="text-end">Purchased</th>
                        <th class="text-end">Returned</th>
                        <th class="text-end">Returnable</th>
                        <th class="text-end">Current Stock</th>
                        <th class="text-end" width="130">Return Qty</th>
                        <th width="220">Reason</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($purchase->details as $detail)
                        @php
                            $returnedQty = $detail->returnedQty();
                            $returnableQty = $detail->returnableQty();
                            $oldQty = old('qty.'.$loop->index, 0);
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->product?->sku }}</td>
                            <td>{{ $detail->product?->product_name }}</td>
                            <td class="text-end">{{ number_format($detail->qty, 2) }}</td>
                            <td class="text-end">{{ number_format($returnedQty, 2) }}</td>
                            <td class="text-end">{{ number_format($returnableQty, 2) }}</td>
                            <td class="text-end">{{ number_format($detail->product?->stock ?? 0, 2) }}</td>
                            <td>
                                <input
                                    type="hidden"
                                    name="purchase_detail_id[]"
                                    value="{{ $detail->id }}">

                                <input
                                    type="number"
                                    name="qty[]"
                                    class="form-control text-end return-qty"
                                    value="{{ $oldQty }}"
                                    min="0"
                                    max="{{ $returnableQty }}"
                                    step="0.01"
                                    data-unit-cost="{{ $detail->unit_cost }}"
                                    {{ $returnableQty <= 0 ? 'readonly' : '' }}>
                            </td>
                            <td>
                                <input
                                    type="text"
                                    name="reason[]"
                                    class="form-control"
                                    value="{{ old('reason.'.$loop->index) }}"
                                    placeholder="Reason">
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="table-primary">
                        <th colspan="8" class="text-end">Return Total</th>
                        <th class="text-end" id="returnTotal">0.00</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="card-footer">
            @error('qty')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-danger">
                <i class="fas fa-undo"></i>
                Save Return
            </button>

            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </div>
</form>

@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateTotal() {
        let total = 0;

        document.querySelectorAll('.return-qty').forEach(function (input) {
            const qty = parseFloat(input.value) || 0;
            const unitCost = parseFloat(input.dataset.unitCost) || 0;

            total += qty * unitCost;
        });

        document.getElementById('returnTotal').innerText = total.toFixed(2);
    }

    document.querySelectorAll('.return-qty').forEach(function (input) {
        input.addEventListener('input', calculateTotal);
    });

    calculateTotal();
});
</script>
@stop
