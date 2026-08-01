{{-- Customer Information --}}

<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">

            <i class="fas fa-user mr-1"></i>

            Customer Information

        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <div class="form-group">

                    <label>Customer</label>

                    <select
                        name="customer_id"
                        id="customer_id"
                        class="form-control select2"
                        style="width:100%;">

                        <option value="">
                            Walk-in Customer
                        </option>

                        @foreach($customers as $customer)

                            <option

                                value="{{ $customer->id }}"

                                data-code="{{ $customer->customer_code }}"

                                data-phone="{{ $customer->phone }}"

                                data-address="{{ $customer->address }}"

                                {{ old('customer_id',$sale->customer_id ?? '')==$customer->id ? 'selected':'' }}

                            >

                                {{ $customer->customer_code }}

                                -

                                {{ $customer->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    <label>Customer Code</label>

                    <input
                        type="text"
                        id="customer_code"
                        class="form-control"
                        readonly>

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    <label>Phone</label>

                    <input
                        type="text"
                        id="customer_phone"
                        class="form-control"
                        readonly>

                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="form-group">

                    <label>Address</label>

                    <textarea
                        id="customer_address"
                        rows="2"
                        class="form-control"
                        readonly></textarea>

                </div>

            </div>

        </div>

    </div>

</div>

@push('js')

<script>

$(function(){

    function loadCustomer(){

        let option =
            $('#customer_id option:selected');

        $('#customer_code')
            .val(option.data('code') || '');

        $('#customer_phone')
            .val(option.data('phone') || '');

        $('#customer_address')
            .val(option.data('address') || '');

    }

    $('#customer_id').change(loadCustomer);

    loadCustomer();

});

</script>

@endpush