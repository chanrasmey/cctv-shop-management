@extends('layouts.admin')

@section('page_title', 'Edit Customer')

@section('content_body')

<div class="card">

<div class="card-header">

<h3 class="card-title">Edit Customer</h3>

</div>

<form method="POST"
      action="{{ route('customers.update',$customer) }}">

@csrf
@method('PUT')

<div class="card-body">

<div class="row">

<div class="col-md-6">

<div class="form-group">

<label>Customer Code *</label>

<input
type="text"
name="customer_code"
class="form-control"
value="{{ old('customer_code',$customer->customer_code) }}">

</div>

<div class="form-group">

<label>Customer Name *</label>

<input
type="text"
name="name"
class="form-control"
value="{{ old('name',$customer->name) }}">

</div>

<div class="form-group">

<label>Phone</label>

<input
type="text"
name="phone"
class="form-control"
value="{{ old('phone',$customer->phone) }}">

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
value="{{ old('email',$customer->email) }}">

</div>

</div>

<div class="col-md-6">

<div class="form-group">

<label>Opening Balance</label>

<input
type="number"
step="0.01"
name="opening_balance"
class="form-control"
value="{{ old('opening_balance',$customer->opening_balance) }}">

</div>

<div class="form-group">

<label>Status</label>

<select
name="status"
class="form-control">

<option value="1" {{ $customer->status ? 'selected' : '' }}>
Active
</option>

<option value="0" {{ !$customer->status ? 'selected' : '' }}>
Inactive
</option>

</select>

</div>

</div>

</div>

<div class="form-group">

<label>Address</label>

<textarea
rows="3"
name="address"
class="form-control">{{ old('address',$customer->address) }}</textarea>

</div>

</div>

<div class="card-footer">

<button class="btn btn-success">

<i class="fas fa-save"></i>

Update Customer

</button>

<a href="{{ route('customers.index') }}"
class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

@endsection