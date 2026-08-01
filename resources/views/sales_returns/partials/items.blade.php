<div class="card card-outline card-success">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-box-open mr-1"></i>

            Return Items

        </h3>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover"
                id="sales-return-items">

                <thead class="thead-light">

                <tr>

                    <th width="4%">#</th>

                    <th>Product</th>
                    <th width="8%">Sold</th>
                    <th width="8%">Returned</th>
                    <th width="8%">Remaining</th>
                    <th width="10%">Return Qty</th>
                    <th width="10%">Unit Price</th>
                    <th width="12%">Subtotal</th>
                    <th width="15%">Reason</th>
                    <th width="15%">Remark</th>

                </tr>

                </thead>

                <tbody>

                @foreach($sale->details as $index => $detail)

                    @php

                        $returned = $detail->salesReturnDetails->sum('qty');

                        $remaining = $detail->qty - $returned;

                    @endphp

                    @if($remaining > 0)

                    <tr>

                        <td class="text-center">

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            <strong>

                                {{ $detail->product->product_name }}

                            </strong>

                            <br>

                            <small class="text-muted">

                                {{ $detail->product->sku }}

                            </small>

                            <input
                                type="hidden"
                                name="sale_detail_id[]"
                                value="{{ $detail->id }}">

                        </td>

                        <td class="text-center">

                            {{ number_format($detail->qty,2) }}

                        </td>

                        <td class="text-center text-danger">

                            {{ number_format($returned,2) }}

                        </td>

                        <td class="text-center text-success">

                            <span
                                class="remaining-qty">

                                {{ number_format($remaining,2) }}

                            </span>

                        </td>

                        <td>

                            <input
                                type="number"
                                name="qty[]"
                                class="form-control return-qty"
                                min="0"
                                max="{{ $remaining }}"
                                step="0.01"
                                value="{{ old('qty.'.$index,0) }}"
                                data-price="{{ $detail->unit_price }}"
                                data-remaining="{{ $remaining }}">

                        </td>

                        <td>

                            <input
                                type="text"
                                class="form-control text-right"
                                value="{{ number_format($detail->unit_price,2) }}"
                                readonly>

                        </td>

                        <td>

                            <input
                                type="text"
                                class="form-control text-right line-subtotal"
                                value="0.00"
                                readonly>

                        </td>

                        <td>

                            <select
                                name="reason[]"
                                class="form-control">

                                <option value="">

                                    -- Select --

                                </option>

                                <option value="Defective">

                                    Defective

                                </option>

                                <option value="Wrong Item">

                                    Wrong Item

                                </option>

                                <option value="Warranty">

                                    Warranty

                                </option>

                                <option value="Customer Cancelled">

                                    Customer Cancelled

                                </option>

                                <option value="Others">

                                    Others

                                </option>

                            </select>

                        </td>

                        <td>

                            <input
                                type="text"
                                name="remark_item[]"
                                class="form-control"
                                maxlength="255">

                        </td>

                    </tr>

                    @endif

                @endforeach

                </tbody>

                <tfoot>

                <tr>

                    <th colspan="7" class="text-right">

                        Return Total

                    </th>

                    <th>

                        <input
                            type="text"
                            id="return-grand-total"
                            class="form-control text-right font-weight-bold"
                            value="0.00"
                            readonly>

                    </th>

                    <th colspan="2"></th>

                </tr>

                </tfoot>

            </table>

        </div>

    </div>

</div>
@push('js')

<script>

function calculateReturnTotals()
{
    let grandTotal = 0;

    $('#sales-return-items tbody tr').each(function(){

        let row = $(this);

        let qty = parseFloat(
            row.find('.return-qty').val()
        ) || 0;

        let remaining = parseFloat(
            row.find('.return-qty').data('remaining')
        ) || 0;

        if(qty < 0){

            qty = 0;

            row.find('.return-qty').val(0);

        }

        if(qty > remaining){

            qty = remaining;

            row.find('.return-qty').val(remaining);

            Swal.fire({

                icon: 'warning',

                title: 'Invalid Quantity',

                text: 'Return quantity cannot exceed the remaining quantity.'

            });

        }

        let price = parseFloat(
            row.find('.return-qty').data('price')
        ) || 0;

        let subtotal = qty * price;

        row.find('.line-subtotal')
            .val(subtotal.toFixed(2));

        grandTotal += subtotal;

    });

    $('#return-grand-total')
        .val(grandTotal.toFixed(2));

    $('#summary-return-total')
        .val(grandTotal.toFixed(2));

    $('#grand_total')
        .val(grandTotal.toFixed(2));

}

$(document).on(

    'keyup change',

    '.return-qty',

    function(){

        calculateReturnTotals();

    }

);

calculateReturnTotals();

</script>

@endpush
@push('js')

<script>

$('#sales-return-form').on('submit', function(e){

    let hasItem = false;

    $('.return-qty').each(function(){

        let qty = parseFloat($(this).val()) || 0;

        if(qty > 0){

            hasItem = true;

        }

    });

    if(!hasItem){

        e.preventDefault();

        Swal.fire({

            icon:'warning',

            title:'No Return Item',

            text:'Please enter a return quantity for at least one product.'

        });

        return false;

    }

    $('.return-qty').each(function(){

        let qty = parseFloat($(this).val()) || 0;

        let row = $(this).closest('tr');

        if(qty <= 0){

            row.find('input,select').prop('disabled', true);

        }

    });

    $(this)
        .find('button[type=submit]')
        .prop('disabled', true)
        .html(
            '<i class="fas fa-spinner fa-spin"></i> Saving...'
        );

});

$(function(){

    calculateReturnTotals();

});

</script>

@endpush