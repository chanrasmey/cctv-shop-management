<div class="card">

    <div class="card-header bg-success">

        <h3 class="card-title">

            <i class="fas fa-box"></i>

            Return Items

        </h3>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover mb-0"
                id="returnTable">

                <thead>

                <tr>

                    <th width="50">
                        #
                    </th>

                    <th width="120">
                        SKU
                    </th>

                    <th>
                        Product
                    </th>

                    <th width="100" class="text-end">
                        Purchased
                    </th>

                    <th width="100" class="text-end">
                        Returned
                    </th>

                    <th width="100" class="text-end">
                        Remaining
                    </th>

                    <th width="120" class="text-end">
                        Return Qty
                    </th>

                    <th width="140" class="text-end">
                        Unit Cost
                    </th>

                    <th width="150" class="text-end">
                        Line Total
                    </th>

                </tr>

                </thead>

                <tbody>

                @foreach($purchase->details as $detail)

                    @php

                        $remaining = $detail->returnableQty();

                        $returned = $detail->returnedQty();

                    @endphp

                    @if($remaining > 0)

                        <tr>

                            <td class="text-center">

                                {{ $loop->iteration }}

                            </td>

                            <td>

                                {{ $detail->product?->sku }}

                            </td>

                            <td>

                                {{ $detail->product?->product_name }}

                            </td>

                            <td class="text-end">

                                {{ number_format($detail->qty,2) }}

                            </td>

                            <td class="text-end text-danger">

                                {{ number_format($returned,2) }}

                            </td>

                            <td class="text-end text-success">

                                {{ number_format($remaining,2) }}

                            </td>

                            <td>

                                <input
                                    type="hidden"
                                    name="purchase_detail_id[]"
                                    value="{{ $detail->id }}">

                                <input
                                    type="number"
                                    name="qty[]"
                                    class="form-control text-end return-qty"
                                    value="0"
                                    min="0"
                                    max="{{ $remaining }}"
                                    step="0.01"
                                    data-max="{{ $remaining }}">

                            </td>
                            <td class="text-end">

                                {{ number_format($detail->unit_cost, 2) }}

                                <input
                                    type="hidden"
                                    name="unit_cost[]"
                                    value="{{ $detail->unit_cost }}">

                            </td>

                            <td class="text-end line-total">

                                0.00

                            </td>

                        </tr>

                    @endif

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>
<div class="card">

    <div class="card-footer">

        <div class="row justify-content-end">

            <div class="col-md-4">

                <table class="table table-bordered">

                    <tr class="table-danger">

                        <th width="45%">

                            Return Total

                        </th>

                        <td>

                            <input
                                type="number"
                                name="subtotal"
                                id="subtotal"
                                class="form-control text-end font-weight-bold"
                                value="0.00"
                                step="0.01"
                                readonly>

                        </td>

                    </tr>

                </table>

            </div>

        </div>

    </div>

</div>
