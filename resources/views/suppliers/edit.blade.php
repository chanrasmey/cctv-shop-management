@extends('layouts.admin')

@section('page_title', 'Edit Supplier')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Edit Supplier</h3>
    </div>

    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">

        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Supplier Code *</label>

                        <input
                            type="text"
                            name="supplier_code"
                            class="form-control"
                            value="{{ old('supplier_code', $supplier->supplier_code) }}">

                        @error('supplier_code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    <div class="form-group">

                        <label>Company Name *</label>

                        <input
                            type="text"
                            name="company_name"
                            class="form-control"
                            value="{{ old('company_name', $supplier->company_name) }}">

                        @error('company_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                    <div class="form-group">

                        <label>Contact Person</label>

                        <input
                            type="text"
                            name="contact_person"
                            class="form-control"
                            value="{{ old('contact_person', $supplier->contact_person) }}">

                    </div>

                    <div class="form-group">

                        <label>Phone</label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone', $supplier->phone) }}">

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Email</label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $supplier->email) }}">

                    </div>

                    <div class="form-group">

                        <label>Tax Number</label>

                        <input
                            type="text"
                            name="tax_number"
                            class="form-control"
                            value="{{ old('tax_number', $supplier->tax_number) }}">

                    </div>

                    <div class="form-group">

                        <label>Opening Balance</label>

                        <input
                            type="number"
                            step="0.01"
                            name="opening_balance"
                            class="form-control"
                            value="{{ old('opening_balance', $supplier->opening_balance) }}">

                    </div>

                    <div class="form-group">

                        <label>Status</label>

                        <select name="status" class="form-control">

                            <option value="1" {{ $supplier->status ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0" {{ !$supplier->status ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>

            </div>

            <div class="form-group">

                <label>Address</label>

                <textarea
                    name="address"
                    rows="3"
                    class="form-control">{{ old('address', $supplier->address) }}</textarea>

            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-success">

                <i class="fas fa-save"></i>

                Update Supplier

            </button>

            <a href="{{ route('suppliers.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection