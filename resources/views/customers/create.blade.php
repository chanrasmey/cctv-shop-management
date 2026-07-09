@extends('layouts.admin')

@section('page_title', 'Create Customer')

@section('content_body')

<div class="card">

<div class="card-header">

<h3 class="card-title">Create Customer</h3>

</div>

<form method="POST"
      action="{{ route('customers.store') }}">

@csrf

<div class="card-body">

<div class="row">

<div class="col-md-6">

<div class="form-group">

<label>Customer Code *</label>

<input
type="text"
name="customer_code"
class="form-control"
value="{{ old('customer_code') }}">

</div>

<div class="form-group">

<label>Customer Name *</label>

<input
type="text"
name="name"
class="form-control"
value="{{ old('name') }}">

</div>

<div class="form-group">

<label>Phone</label>

<input
type="text"
name="phone"
class="form-control"
value="{{ old('phone') }}">

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
value="{{ old('email') }}">

</div>

</div>

<div class="col-md-6">

<div class="form-group">

<label>Opening Balance</label>

<input
type="number"
step="0.01"
name="opening_balance"
value="0"
class="form-control">

</div>

<div class="form-group">

<label>Status</label>

<select
name="status"
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
rows="3"
name="address"
class="form-control">{{ old('address') }}</textarea>

</div>

</div>

<div class="card-footer">

<button class="btn btn-primary">

<i class="fas fa-save"></i>

Save Customer

</button>

<a href="{{ route('customers.index') }}"
class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

@endsection