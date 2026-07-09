@php
    $isEdit = isset($purchase);
    $oldProductIds = old('product_id');
    $rows = [];

    if (is_array($oldProductIds)) {
        foreach ($oldProductIds as $index => $productId) {
            $rows[] = [
                'product_id' => $productId,
                'qty' => old('qty.'.$index, 1),
                'unit_cost' => old('unit_cost.'.$index, 0),
                'discount_percent' => old('discount_percent_item.'.$index, 0),
                'discount_amount' => old('discount_amount_item.'.$index, 0),
                'subtotal' => old('subtotal_item.'.$index, 0),
            ];
        }
    } elseif ($isEdit) {
        foreach ($purchase->details as $detail) {
            $rows[] = [
                'product_id' => $detail->product_id,
                'qty' => $detail->qty,
                'unit_cost' => $detail->unit_cost,
                'discount_percent' => $detail->discount_percent,
                'discount_amount' => $detail->discount_amount,
                'subtotal' => $detail->subtotal,
            ];
        }
    }

    if (count($rows) === 0) {
        $rows[] = [
            'product_id' => '',
            'qty' => 1,
            'unit_cost' => 0,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'subtotal' => 0,
        ];
    }
@endphp

<div class="card">

    <div class="card-header bg-success">

        <h3 class="card-title">
            <i class="fas fa-box"></i>
            Purchase Items
        </h3>

        <div class="card-tools">

            <button
                type="button"
                class="btn btn-primary btn-sm"
                id="btnAddRow">

                <i class="fas fa-plus"></i>

                Add Product

            </button>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0" id="purchaseTable">

                <thead>

                <tr>

                    <th width="40">#</th>

                    <th width="120">SKU</th>

                    <th>Product</th>

                    <th width="90">Stock</th>

                    <th width="100">Qty</th>

                    <th width="120">Unit Cost</th>

                    <th width="100">Disc %</th>

                    <th width="120">Disc Amt</th>

                    <th width="140">Subtotal</th>

                    <th width="60"></th>

                </tr>

                </thead>

                <tbody id="purchaseItems">

                @foreach($rows as $row)
                    @php
                        $selectedProduct = $products->firstWhere('id', (int) $row['product_id']);
                    @endphp

                    <tr>

                        <td class="row-number text-center">{{ $loop->iteration }}</td>

                        <td>

                            <input
                                type="text"
                                class="form-control sku"
                                value="{{ $selectedProduct?->sku }}"
                                readonly>

                        </td>

                        <td>

                            <select
                                name="product_id[]"
                                class="form-control product-select"
                                required>

                                <option value="">Select Product</option>

                                @foreach($products as $product)

                                    <option
                                        value="{{ $product->id }}"
                                        data-sku="{{ $product->sku }}"
                                        data-stock="{{ $product->stock }}"
                                        data-price="{{ $product->buy_price }}"
                                        {{ (string) $row['product_id'] === (string) $product->id ? 'selected' : '' }}>

                                        {{ $product->product_name }}

                                    </option>

                                @endforeach

                            </select>

                        </td>

                        <td>

                            <input
                                class="form-control current-stock text-center"
                                value="{{ $selectedProduct?->stock }}"
                                readonly>

                        </td>

                        <td>

                            <input
                                type="number"
                                name="qty[]"
                                class="form-control qty text-end"
                                value="{{ $row['qty'] }}"
                                min="1">

                        </td>

                        <td>

                            <input
                                type="number"
                                name="unit_cost[]"
                                class="form-control unit-cost text-end"
                                value="{{ $row['unit_cost'] }}"
                                step="0.01">

                        </td>

                        <td>

                            <input
                                type="number"
                                name="discount_percent_item[]"
                                class="form-control discount-percent text-end"
                                value="{{ $row['discount_percent'] }}"
                                step="0.01">

                        </td>

                        <td>

                            <input
                                type="number"
                                name="discount_amount_item[]"
                                class="form-control discount-amount text-end"
                                value="{{ $row['discount_amount'] }}"
                                step="0.01">

                        </td>

                        <td>

                            <input
                                type="number"
                                name="subtotal_item[]"
                                class="form-control subtotal text-end"
                                value="{{ $row['subtotal'] }}"
                                readonly>

                        </td>

                        <td class="text-center">

                            <button
                                type="button"
                                class="btn btn-danger btn-sm btnRemoveRow">

                                <i class="fas fa-trash"></i>

                            </button>

                        </td>

                    </tr>
                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>
