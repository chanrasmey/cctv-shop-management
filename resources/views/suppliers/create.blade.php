@extends('layouts.admin')

@section('page_title', 'Create Supplier')

@section('content_body')

<div class="card">

<div class="card-header">

<h3 class="card-title">Create Supplier</h3>

</div>

<form method="POST"
      action="{{ route('suppliers.store') }}">

@csrf

<div class="card-body">

<div class="row">

<div class="col-md-6">

<div class="form-group">
<label>Supplier Code *</label>
<input type="text"
       name="supplier_code"
       class="form-control"
       value="{{ old('supplier_code') }}">
</div>

<div class="form-group">
<label>Company Name *</label>
<input type="text"
       name="company_name"
       class="form-control"
       value="{{ old('company_name') }}">
</div>

<div class="form-group">
<label>Contact Person</label>
<input type="text"
       name="contact_person"
       class="form-control"
       value="{{ old('contact_person') }}">
</div>

<div class="form-group">
<label>Phone</label>
<input type="text"
       name="phone"
       class="form-control"
       value="{{ old('phone') }}">
</div>

</div>

<div class="col-md-6">

<div class="form-group">
<label>Email</label>
<input type="email"
       name="email"
       class="form-control"
       value="{{ old('email') }}">
</div>

<div class="form-group">
<label>Tax Number</label>
<input type="text"
       name="tax_number"
       class="form-control"
       value="{{ old('tax_number') }}">
</div>

<div class="form-group">
<label>Opening Balance</label>
<input type="number"
       step="0.01"
       name="opening_balance"
       value="0"
       class="form-control">
</div>

<div class="form-group">
<label>Status</label>

<select name="status"
        class="form-control">

<option value="1">Active</option>
<option value="0">Inactive</option>

</select>

</div>

</div>

</div>

<div class="form-group">

<label>Address</label>

<textarea
    name="address"
    rows="3"
    class="form-control">{{ old('address') }}</textarea>

</div>

</div>

<div class="card-footer">

<button class="btn btn-primary">

<i class="fas fa-save"></i>

Save Supplier

</button>

<a href="{{ route('suppliers.index') }}"
   class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

@endsection