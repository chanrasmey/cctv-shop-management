@extends('layouts.admin')

@section('page_title', 'Edit Brand')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Edit Brand</h3>
    </div>

    <form action="{{ route('brands.update',$brand) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Brand Code</label>

                        <input type="text"
                               name="code"
                               class="form-control"
                               value="{{ old('code',$brand->code) }}">

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
                               value="{{ old('name',$brand->name) }}">

                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

            </div>

            <div class="form-group">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="1" {{ $brand->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$brand->status ? 'selected' : '' }}>Inactive</option>

                </select>

            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-success">
                <i class="fas fa-save"></i>
                Update Brand
            </button>

            <a href="{{ route('brands.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection