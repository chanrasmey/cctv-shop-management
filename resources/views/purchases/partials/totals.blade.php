@php
    $isEdit = isset($purchase);

    $subtotal = old('subtotal', $isEdit ? $purchase->subtotal : '0.00');
    $discountPercent = old('discount_percent', $isEdit ? $purchase->discount_percent : 0);
    $discountAmount = old('discount_amount', $isEdit ? $purchase->discount_amount : '0.00');
    $taxPercent = old('tax_percent', $isEdit ? $purchase->tax_percent : 0);
    $taxAmount = old('tax_amount', $isEdit ? $purchase->tax_amount : '0.00');
    $grandTotal = old('grand_total', $isEdit ? $purchase->grand_total : '0.00');
    $paidAmount = old('paid_amount', $isEdit ? $purchase->paid_amount : '0.00');
    $balance = old('balance', $isEdit ? $purchase->balance : '0.00');
@endphp

<div class="card">

    <div class="card-header bg-warning">

        <h3 class="card-title">

            <i class="fas fa-calculator"></i>

            Purchase Summary

        </h3>

    </div>

    <div class="card-body">

        <div class="row justify-content-end">

            <div class="col-md-5">

                <table class="table table-bordered">

                    <tr>

                        <th width="45%">
                            Subtotal
                        </th>

                        <td>

                            <input
                                type="number"
                                name="subtotal"
                                id="subtotal"
                                class="form-control text-end"
                                value="{{ $subtotal }}"
                                step="0.01"
                                readonly>

                        </td>

                    </tr>

                    <tr>

                        <th>
                            Invoice Discount %
                        </th>

                        <td>

                            <input
                                type="number"
                                name="discount_percent"
                                id="discount_percent"
                                class="form-control text-end"
                                value="{{ $discountPercent }}"
                                step="0.01">

                        </td>

                    </tr>

                    <tr>

                        <th>
                            Invoice Discount
                        </th>

                        <td>

                            <input
                                type="number"
                                name="discount_amount"
                                id="discount_amount"
                                class="form-control text-end"
                                value="{{ $discountAmount }}"
                                step="0.01">

                        </td>

                    </tr>

                    <tr>

                        <th>
                            Tax %
                        </th>

                        <td>

                            <input
                                type="number"
                                name="tax_percent"
                                id="tax_percent"
                                class="form-control text-end"
                                value="{{ $taxPercent }}"
                                step="0.01">

                        </td>

                    </tr>

                    <tr>

                        <th>
                            Tax Amount
                        </th>

                        <td>

                            <input
                                type="number"
                                name="tax_amount"
                                id="tax_amount"
                                class="form-control text-end"
                                value="{{ $taxAmount }}"
                                step="0.01"
                                readonly>

                        </td>

                    </tr>

                    <tr class="table-primary">

                        <th>

                            Grand Total

                        </th>

                        <td>

                            <input
                                type="number"
                                name="grand_total"
                                id="grand_total"
                                class="form-control text-end font-weight-bold"
                                value="{{ $grandTotal }}"
                                readonly>

                        </td>

                    </tr>

                    <tr>

                        <th>

                            Paid Amount

                        </th>

                        <td>

                            <input
                                type="number"
                                name="paid_amount"
                                id="paid_amount"
                                class="form-control text-end"
                                value="{{ $paidAmount }}"
                                step="0.01">

                        </td>

                    </tr>

                    <tr class="table-danger">

                        <th>

                            Balance

                        </th>

                        <td>

                            <input
                                type="number"
                                name="balance"
                                id="balance"
                                class="form-control text-end font-weight-bold"
                                value="{{ $balance }}"
                                readonly>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

    <div class="card-footer">

        <button
            type="submit"
            class="btn btn-success">

            <i class="fas fa-save"></i>

            Save Purchase

        </button>

        <a
            href="{{ route('purchases.index') }}"
            class="btn btn-secondary">

            Cancel

        </a>

    </div>

</div>
