@php

$returnDate = old(
    'return_date',
    now()->format('Y-m-d')
);

$remark = old(
    'remark',
    ''
);

@endphp

<div class="card">

    <div class="card-header bg-danger">

        <h3 class="card-title">

            <i class="fas fa-undo"></i>

            Purchase Return Information

        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Return No
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $returnNo }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Return Date
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="return_date"
                        class="form-control"
                        value="{{ $returnDate }}"
                        required>

                    @error('return_date')

                        <small class="text-danger">

                            {{ $message }}

                        </small>

                    @enderror

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Purchase No
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $purchase->purchase_no }}"
                        readonly>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Purchase Date
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $purchase->purchase_date->format('d-m-Y') }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Supplier
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $purchase->supplier->company_name }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>
                        Purchase Status
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $purchase->status }}"
                        readonly>

                </div>

            </div>

        </div>

        <input
            type="hidden"
            name="purchase_id"
            value="{{ $purchase->id }}">

        <input
            type="hidden"
            name="supplier_id"
            value="{{ $purchase->supplier_id }}">

        <div class="form-group">

            <label>

                Remark

            </label>

            <textarea
                name="remark"
                rows="3"
                class="form-control"
                placeholder="Reason for purchase return...">{{ $remark }}</textarea>

        </div>

    </div>

</div>