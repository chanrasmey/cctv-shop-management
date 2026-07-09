@php
    $isEdit = isset($purchase);

    $purchaseNumber = $isEdit
        ? $purchase->purchase_no
        : ($purchaseNo ?? '');

    $purchaseDate = old(
        'purchase_date',
        $isEdit && $purchase->purchase_date
            ? $purchase->purchase_date->format('Y-m-d')
            : now()->format('Y-m-d')
    );

    $supplierId = old(
        'supplier_id',
        $isEdit ? $purchase->supplier_id : ''
    );

    $status = old(
        'status',
        $isEdit ? $purchase->status : 'Draft'
    );

    $invoiceNo = old(
        'invoice_no',
        $isEdit ? $purchase->invoice_no : ''
    );

    $remark = old(
        'remark',
        $isEdit ? $purchase->remark : ''
    );
@endphp

<div class="card">

    <div class="card-header bg-primary">

        <h3 class="card-title">

            <i class="fas fa-shopping-cart"></i>

            {{ $isEdit ? 'Edit Purchase' : 'Purchase Information' }}

        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <div class="form-group">

                    <label>Purchase No</label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $purchaseNumber }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>

                        Purchase Date

                        <span class="text-danger">*</span>

                    </label>

                    <input
                        type="date"
                        name="purchase_date"
                        class="form-control"
                        value="{{ $purchaseDate }}"
                        required>

                    @error('purchase_date')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>Status</label>

                    <select
                        name="status"
                        class="form-control">

                        @foreach(['Draft','Pending','Completed'] as $value)

                            <option
                                value="{{ $value }}"
                                {{ $status === $value ? 'selected' : '' }}>

                                {{ $value }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-6">

                <div class="form-group">

                    <label>

                        Supplier

                        <span class="text-danger">*</span>

                    </label>

                    <select
                        name="supplier_id"
                        class="form-control"
                        required>

                        <option value="">

                            -- Select Supplier --

                        </option>

                        @foreach($suppliers as $supplier)

                            <option
                                value="{{ $supplier->id }}"
                                {{ (string)$supplierId === (string)$supplier->id ? 'selected' : '' }}>

                                {{ $supplier->company_name }}

                            </option>

                        @endforeach

                    </select>

                    @error('supplier_id')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group">

                    <label>Invoice No</label>

                    <input
                        type="text"
                        name="invoice_no"
                        class="form-control"
                        value="{{ $invoiceNo }}"
                        placeholder="Supplier Invoice Number">

                </div>

            </div>

        </div>

        <div class="form-group">

            <label>Remark</label>

            <textarea
                name="remark"
                rows="3"
                class="form-control"
                placeholder="Optional purchase remark...">{{ $remark }}</textarea>

        </div>

    </div>

</div>