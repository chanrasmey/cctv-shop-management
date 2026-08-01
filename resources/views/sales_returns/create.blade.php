@extends('adminlte::page')

@section('title', 'New Sales Return')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h1 class="mb-1">

            <i class="fas fa-undo-alt text-danger"></i>

            New Sales Return

        </h1>

        <small class="text-muted">

            Process customer product returns

        </small>

    </div>

    <a href="{{ route('sales-returns.index') }}"
       class="btn btn-secondary">

        <i class="fas fa-arrow-left mr-1"></i>

        Back

    </a>

</div>

@stop

@section('content')

@include('partials.alert')

<form
    method="POST"
    action="{{ route('sales-returns.store') }}"
    id="sales-return-form">

    @csrf

    <div class="row">

        <div class="col-lg-8">

            <div class="card card-outline card-danger">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-file-invoice mr-1"></i>

                        Sales Return Information

                    </h3>

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>

                                    Sale

                                    <span class="text-danger">*</span>

                                </label>

                                <select
                                    name="sale_id"
                                    id="sale_id"
                                    class="form-control"
                                    required>

                                    <option value="">

                                        -- Select Sale --

                                    </option>

                                    @foreach($sales as $sale)

                                        <option
                                            value="{{ $sale->id }}"
                                            data-customer="{{ $sale->customer?->customer_name }}"
                                            data-customer-id="{{ $sale->customer_id }}"
                                            data-date="{{ optional($sale->sale_date)->format('Y-m-d') }}"
                                            @selected(old('sale_id') == $sale->id)>

                                            {{ $sale->sale_no }}
                                            -
                                            {{ $sale->customer?->customer_name }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>

                                    Return Date

                                </label>

                                <input
                                    type="date"
                                    name="return_date"
                                    class="form-control"
                                    value="{{ old('return_date', now()->format('Y-m-d')) }}"
                                    required>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label>

                                    Customer

                                </label>

                                <input
                                    type="text"
                                    id="customer_name"
                                    class="form-control"
                                    readonly>

                                <input
                                    type="hidden"
                                    name="customer_id"
                                    id="customer_id"
                                    value="{{ old('customer_id') }}">

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="card card-outline card-danger">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-boxes mr-1"></i>

                        Return Items

                    </h3>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table
                            class="table table-bordered table-hover mb-0"
                            id="items-table">

                            <thead>

                                <tr>

                                    <th width="40">

                                        #

                                    </th>

                                    <th>

                                        Product

                                    </th>

                                    <th width="120">

                                        Sold Qty

                                    </th>

                                    <th width="120">

                                        Returned

                                    </th>

                                    <th width="120">

                                        Remaining

                                    </th>

                                    <th width="120">

                                        Return Qty

                                    </th>

                                    <th width="140">

                                        Unit Price

                                    </th>

                                    <th width="160">

                                        Subtotal

                                    </th>

                                </tr>

                            </thead>

                            <tbody id="return-items">

                                <tr>

                                    <td colspan="8"
                                        class="text-center text-muted py-5">

                                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>

                                        <br>

                                        Please select a Sale first.

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        {{-- PART 2 STARTS HERE --}}
                <div class="col-lg-4">

            <div class="card card-outline card-primary">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-calculator mr-1"></i>

                        Return Summary

                    </h3>

                </div>

                <div class="card-body">

                    <table class="table table-sm">

                        <tr>

                            <th>Total Items</th>

                            <td class="text-right">

                                <span id="total_items">0</span>

                            </td>

                        </tr>

                        <tr>

                            <th>Total Quantity</th>

                            <td class="text-right">

                                <span id="total_qty">0.00</span>

                            </td>

                        </tr>

                        <tr>

                            <th>Subtotal</th>

                            <td class="text-right">

                                <strong>

                                    $ <span id="subtotal_display">0.00</span>

                                </strong>

                            </td>

                        </tr>

                    </table>

                    <input
                        type="hidden"
                        name="subtotal"
                        id="subtotal"
                        value="0">

                </div>

            </div>

            <div class="card card-outline card-secondary">

                <div class="card-header">

                    <h3 class="card-title">

                        <i class="fas fa-comment-dots mr-1"></i>

                        Return Remark

                    </h3>

                </div>

                <div class="card-body">

                    <div class="form-group mb-0">

                        <textarea
                            name="remark"
                            rows="5"
                            class="form-control"
                            placeholder="Optional remark...">{{ old('remark') }}</textarea>

                    </div>

                </div>

            </div>

            <div class="card">

                <div class="card-body">

                    <button
                        type="submit"
                        class="btn btn-danger btn-block">

                        <i class="fas fa-save mr-1"></i>

                        Save Sales Return

                    </button>

                    <a
                        href="{{ route('sales-returns.index') }}"
                        class="btn btn-secondary btn-block">

                        <i class="fas fa-times mr-1"></i>

                        Cancel

                    </a>

                </div>

            </div>

        </div>

    </div>

</form>

{{-- Hidden Sale Data --}}
@foreach($sales as $sale)

    <script
        type="application/json"
        id="sale-data-{{ $sale->id }}">

{!! json_encode(

    $sale->details->map(function ($detail) {

        return [

    'id' => $detail->id,

    'product_id' => $detail->product_id,

    'product_name' => $detail->product?->product_name,

    'qty' => (float) $detail->qty,

    'returned_qty' => (float) ($detail->returned_qty ?? 0),

    'remaining_qty' => (float) ($detail->remaining_qty ?? $detail->qty),

    'unit_price' => (float) $detail->unit_price,

    'unit_cost' => (float) $detail->unit_cost,

];
    }),

    JSON_PRETTY_PRINT

) !!}

    </script>

@endforeach

{{-- PART 3 STARTS HERE --}}
@section('js')

<script>

const saleSelect = document.getElementById('sale_id');
const customerName = document.getElementById('customer_name');
const customerId = document.getElementById('customer_id');
const itemsTable = document.getElementById('return-items');

saleSelect.addEventListener('change', function () {

    customerName.value =
        this.options[this.selectedIndex].dataset.customer || '';

    customerId.value =
        this.options[this.selectedIndex].dataset.customerId || '';

    loadSaleItems(this.value);

});

function loadSaleItems(saleId)
{
    itemsTable.innerHTML = '';

    if (!saleId) {

        itemsTable.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-5">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i><br>
                    Please select a Sale first.
                </td>
            </tr>
        `;

        calculateSummary();

        return;
    }

    const source = document.getElementById(
        'sale-data-' + saleId
    );

    if (!source) {

        calculateSummary();

        return;
    }

    const items = JSON.parse(source.textContent);

    items.forEach(function(item,index){

        itemsTable.insertAdjacentHTML(
            'beforeend',
            rowTemplate(item,index)
        );

    });

    bindEvents();

    calculateSummary();
}

function rowTemplate(item,index)
{
    return `

<tr>

<td>

${index+1}

<input
type="hidden"
name="sale_detail_id[]"
value="${item.id}">

<input
type="hidden"
name="product_id[]"
value="${item.product_id}">

</td>

<td>

<strong>${item.product_name}</strong>

</td>

<td class="text-center">

${Number(item.qty).toFixed(2)}

</td>

<td class="text-center">

${Number(item.returned_qty).toFixed(2)}

</td>

<td class="text-center">

${Number(item.remaining_qty).toFixed(2)}

</td>

<td>

<input
type="number"
step="0.01"
min="0"
max="${item.remaining_qty}"
value="0"
name="qty[]"
class="form-control form-control-sm qty">

</td>

<td>

<input
type="text"
class="form-control form-control-sm unit-price"
readonly
value="${Number(item.unit_price).toFixed(2)}">

<input
type="hidden"
name="unit_price[]"
value="${item.unit_price}">

</td>

<td>

<input
type="text"
class="form-control form-control-sm subtotal"
readonly
value="0.00">

</td>

</tr>

`;

}

function bindEvents()
{
    document.querySelectorAll('.qty')
        .forEach(function(input){

        input.addEventListener(
            'input',
            calculateSummary
        );

    });
}
function calculateSummary()
{
    let totalItems = 0;
    let totalQty = 0;
    let subtotal = 0;

    document.querySelectorAll('#return-items tr').forEach(function(row){

        const qtyInput = row.querySelector('.qty');

        if (!qtyInput) {
            return;
        }

        let qty = parseFloat(qtyInput.value);

const max = parseFloat(qtyInput.max) || 0;

if (isNaN(qty)) {
    qty = 0;
}

qty = Math.min(Math.max(qty, 0), max);

// Normalize the input so the UI always shows the valid value
qtyInput.value = qty;

const finalQty = qty;

        const unitPrice = parseFloat(
            row.querySelector('input[name="unit_price[]"]').value
        ) || 0;

        const lineTotal = finalQty * unitPrice;

        row.querySelector('.subtotal').value =
            lineTotal.toFixed(2);

        if (finalQty > 0) {

            totalItems++;

            totalQty += finalQty;

            subtotal += lineTotal;

        }

    });

    document.getElementById('total_items').textContent =
        totalItems;

    document.getElementById('total_qty').textContent =
        totalQty.toFixed(2);

    document.getElementById('subtotal_display').textContent =
        subtotal.toFixed(2);

    document.getElementById('subtotal').value =
        subtotal.toFixed(2);
}

/*
|--------------------------------------------------------------------------
| Restore old values after validation error
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Restore old values after validation error
|--------------------------------------------------------------------------
*/

function restoreOldInput() {

    @if(old('sale_id'))

    saleSelect.value = "{{ old('sale_id') }}";

    const selectedOption = saleSelect.options[saleSelect.selectedIndex];

    if (selectedOption) {

        customerName.value = selectedOption.dataset.customer || '';

        customerId.value = selectedOption.dataset.customerId || '';

    }

    loadSaleItems("{{ old('sale_id') }}");

    const oldQty = @json(old('qty', []));

    requestAnimationFrame(function () {

        document.querySelectorAll('.qty').forEach(function (input, index) {

            if (oldQty[index] !== undefined) {

                input.value = oldQty[index];

            }

        });

        calculateSummary();

    });

    @else

    calculateSummary();

    @endif

}

/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {

    restoreOldInput();

});
</script>

@stop