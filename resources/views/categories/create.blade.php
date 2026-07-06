@extends('layouts.admin')

@section('page_title', 'Create Category')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Create New Category</h3>
    </div>

    <form action="{{ route('categories.store') }}" method="POST">

        @csrf

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Category Code <span class="text-danger">*</span></label>

                        <input type="text"
                               name="code"
                               class="form-control"
                               value="{{ old('code') }}"
                               placeholder="CAT001">

                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Category Name <span class="text-danger">*</span></label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               placeholder="CCTV Camera">

                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

            </div>

            <div class="form-group">

                <label>Description</label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="3">{{ old('description') }}</textarea>

            </div>

            <div class="form-group">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="1" selected>Active</option>
                    <option value="0">Inactive</option>

                </select>

            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-primary">

                <i class="fas fa-save"></i>

                Save Category

            </button>

            <a href="{{ route('categories.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection