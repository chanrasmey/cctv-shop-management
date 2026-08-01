<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-user mr-1"></i>

            Customer Information

        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-4">

                <div class="form-group">

                    <label>Customer</label>

                    <input
                        type="text"
                        class="form-control readonly-input"
                        value="{{ $sale->customer->name }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-2">

                <div class="form-group">

                    <label>Customer Code</label>

                    <input
                        type="text"
                        class="form-control readonly-input"
                        value="{{ $sale->customer->customer_code ?? '-' }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    <label>Phone</label>

                    <input
                        type="text"
                        class="form-control readonly-input"
                        value="{{ $sale->customer->phone ?? '-' }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="text"
                        class="form-control readonly-input"
                        value="{{ $sale->customer->email ?? '-' }}"
                        readonly>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-8">

                <div class="form-group mb-0">

                    <label>Address</label>

                    <textarea
                        class="form-control readonly-input"
                        rows="2"
                        readonly>{{ $sale->customer->address ?? '-' }}</textarea>

                </div>

            </div>

            <div class="col-md-2">

                <div class="form-group mb-0">

                    <label>Sale No.</label>

                    <input
                        type="text"
                        class="form-control readonly-input"
                        value="{{ $sale->sale_no }}"
                        readonly>

                </div>

            </div>

            <div class="col-md-2">

                <div class="form-group mb-0">

                    <label>Sale Date</label>

                    <input
                        type="text"
                        class="form-control readonly-input"
                        value="{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}"
                        readonly>

                </div>

            </div>

        </div>

    </div>

</div>