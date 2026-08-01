@extends('adminlte::page')

@section('title', 'Company Settings')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1 class="m-0">

        <i class="fas fa-building mr-2"></i>

        Company Settings

    </h1>

</div>

@stop

@section('content')

@if(session('success'))

<div class="alert alert-success">

    {{ session('success') }}

</div>

@endif

<form
    action="{{ route('settings.company.update') }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @method('PUT')

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                General Information

            </h3>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Company Name</label>

                        <input
                            type="text"
                            name="company_name"
                            class="form-control"
                            value="{{ old('company_name',$setting->company_name) }}">

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Khmer Company Name</label>

                        <input
                            type="text"
                            name="company_name_kh"
                            class="form-control"
                            value="{{ old('company_name_kh',$setting->company_name_kh) }}">

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Owner</label>

                        <input
                            type="text"
                            name="owner_name"
                            class="form-control"
                            value="{{ old('owner_name',$setting->owner_name) }}">

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Phone</label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone',$setting->phone) }}">

                    </div>

                </div>

                <div class="col-md-12">

                    <div class="form-group">

                        <label>Address</label>

                        <textarea
                            name="address"
                            rows="3"
                            class="form-control">{{ old('address',$setting->address) }}</textarea>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                Contact

            </h3>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    <div class="form-group">

                        <label>Email</label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email',$setting->email) }}">

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="form-group">

                        <label>Website</label>

                        <input
                            type="text"
                            name="website"
                            class="form-control"
                            value="{{ old('website',$setting->website) }}">

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="form-group">

                        <label>Mobile</label>

                        <input
                            type="text"
                            name="mobile"
                            class="form-control"
                            value="{{ old('mobile',$setting->mobile) }}">

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                Logo

            </h3>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4">

                    @if($setting->logo)

                        <img
                            src="{{ $setting->logo_url }}"
                            class="img-fluid border mb-3">

                    @endif

                    <input
                        type="file"
                        name="logo"
                        class="form-control">

                </div>

                <div class="col-md-4">

                    @if($setting->favicon)

                        <img
                            src="{{ $setting->favicon_url }}"
                            class="img-fluid border mb-3">

                    @endif

                    <input
                        type="file"
                        name="favicon"
                        class="form-control">

                </div>

            </div>

        </div>

    </div>

    <div class="card">

        <div class="card-footer text-right">

            <button
                class="btn btn-primary">

                <i class="fas fa-save"></i>

                Save Settings

            </button>

        </div>

    </div>

</form>

@stop