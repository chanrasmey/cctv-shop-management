{{-- ==========================================================
| Sales Items
| Shared by:
| - resources/views/sales/create.blade.php
| - resources/views/sales/edit.blade.php
========================================================== --}}

<div class="card card-outline card-success">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-shopping-cart mr-2"></i>

            Sale Items

        </h3>

    </div>

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-12">

                <label>

                    Search Product

                </label>

                <select
                    id="product_selector"
                    class="form-control select2"
                    style="width:100%;">

                    <option value="">

                        -- Search Product --

                    </option>

                    @foreach($products as $product)

                        <option

                            value="{{ $product->id }}"

                            data-id="{{ $product->id }}"

                            data-sku="{{ $product->sku }}"

                            data-name="{{ $product->product_name }}"

                            data-barcode="{{ $product->barcode }}"

                            data-unit="{{ optional($product->unit)->name }}"

                            data-stock="{{ $product->stock }}"

                            data-price="{{ $product->sell_price }}">

                            {{ $product->sku }}

                            |

                            {{ $product->product_name }}

                            |

                            Stock :

                            {{ number_format($product->stock,2) }}

                        </option>

                    @endforeach

                </select>

            </div>

        </div>

        <div class="table-responsive">

            <table
                class="table table-bordered table-hover table-striped"
                id="sale-items-table">

                <thead class="thead-light">

                <tr>

                    <th width="45">

                        #

                    </th>

                    <th width="120">

                        SKU

                    </th>

                    <th>

                        Product

                    </th>

                    <th width="150">

                        Barcode

                    </th>

                    <th width="80">

                        Unit

                    </th>

                    <th width="90" class="text-center">

                        Stock

                    </th>

                    <th width="90">

                        Qty

                    </th>

                    <th width="130">

                        Unit Price

                    </th>

                    <th width="90">

                        Disc %

                    </th>

                    <th width="140">

                        Line Total

                    </th>

                    <th width="60">

                        Action

                    </th>

                </tr>

                </thead>

                <tbody id="sale-items-body">

                @if(isset($sale))

                    @foreach($sale->details as $index => $detail)

                        <tr>

                            <td class="row-number text-center">

                                {{ $loop->iteration }}

                            </td>

                            <td>

                                {{ $detail->product->sku }}

                            </td>

                            <td>

                                <strong>

                                    {{ $detail->product->product_name }}

                                </strong>

                            </td>

                            <td>

                                {{ $detail->product->barcode }}

                            </td>

                            <td>

                                {{ optional($detail->product->unit)->name }}

                            </td>

                            <td class="text-center">

                                {{ number_format($detail->product->stock + $detail->qty,2) }}

                            </td>

                            <td>

                                <input
                                    type="number"
                                    class="form-control qty"
                                    min="0.01"
                                    step="0.01"
                                    name="items[{{ $index }}][qty]"
                                    value="{{ old("items.$index.qty",$detail->qty) }}">

                                <input
                                    type="hidden"
                                    class="product-id"
                                    name="items[{{ $index }}][product_id]"
                                    value="{{ $detail->product_id }}">

                            </td>

                            <td>

                                <input
                                    type="number"
                                    class="form-control unit-price text-right"
                                    min="0"
                                    step="0.01"
                                    name="items[{{ $index }}][unit_price]"
                                    value="{{ old("items.$index.unit_price",$detail->unit_price) }}">

                            </td>

                            <td>

                                <input
                                    type="number"
                                    class="form-control discount-percent text-right"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    name="items[{{ $index }}][discount_percent]"
                                    value="{{ old("items.$index.discount_percent",$detail->discount_percent) }}">

                                <input
                                    type="hidden"
                                    class="discount-amount"
                                    name="items[{{ $index }}][discount_amount]"
                                    value="{{ old("items.$index.discount_amount",$detail->discount_amount) }}">

                            </td>

                            <td>

                                <input
                                    type="text"
                                    class="form-control subtotal text-right"
                                    value="{{ number_format($detail->subtotal,2,'.','') }}"
                                    readonly>

                            </td>

                            <td class="text-center">

                                <button
                                    type="button"
                                    class="btn btn-danger btn-sm btn-remove-item">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </td>

                        </tr>

                    @endforeach

                @endif

                </tbody>
                <tr id="empty-row"
                    @if(isset($sale) && $sale->details->count())
                        style="display:none;"
                    @endif>

                    <td colspan="11"
                        class="text-center text-muted py-4">

                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>

                        <br>

                        No sale items added.

                    </td>

                </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- ==========================================================
| Template Row
| Used by JavaScript when adding new items.
========================================================== --}}

<template id="sale-item-template">

    <tr>

        <td class="row-number text-center"></td>

        <td class="sku"></td>

        <td class="product-name"></td>

        <td class="barcode"></td>

        <td class="unit text-center"></td>

        <td class="stock text-center"></td>

        <td>

            <input
                type="number"
                class="form-control qty"
                min="0.01"
                step="0.01"
                value="1">

            <input
                type="hidden"
                class="product-id">

        </td>

        <td>

            <input
                type="number"
                class="form-control unit-price text-right"
                min="0"
                step="0.01"
                value="0">

        </td>

        <td>

            <input
                type="number"
                class="form-control discount-percent text-right"
                min="0"
                max="100"
                step="0.01"
                value="0">

            <input
                type="hidden"
                class="discount-amount"
                value="0">

        </td>

        <td>

            <input
                type="text"
                class="form-control subtotal text-right"
                value="0.00"
                readonly>

        </td>

        <td class="text-center">

            <button
                type="button"
                class="btn btn-danger btn-sm btn-remove-item">

                <i class="fas fa-trash"></i>

            </button>

        </td>

    </tr>

</template>