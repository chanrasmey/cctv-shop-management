@extends('layouts.admin')

@section('page_title', 'Create Brand')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Create Brand</h3>
    </div>

    <form action="{{ route('brands.store') }}" method="POST">

        @csrf

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Brand Code</label>

                        <input type="text"
                               name="code"
                               class="form-control"
                               value="{{ old('code') }}"
                               placeholder="BR001">

                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Brand Name</label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               placeholder="Hikvision">

                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

            </div>

            <div class="form-group">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="1">Active</option>
                    <option value="0">Inactive</option>

                </select>

            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Brand
            </button>

            <a href="{{ route('brands.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection