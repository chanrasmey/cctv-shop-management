@extends('layouts.admin')

@section('page_title', 'Edit Category')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Edit Category</h3>
    </div>

    <form action="{{ route('categories.update', $category) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Category Code</label>

                        <input
                            type="text"
                            name="code"
                            class="form-control"
                            value="{{ old('code', $category->code) }}">

                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Category Name</label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $category->name) }}">

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
                    rows="3"
                    class="form-control">{{ old('description', $category->description) }}</textarea>

            </div>

            <div class="form-group">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="1" {{ $category->status ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0" {{ !$category->status ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-success">
                <i class="fas fa-save"></i>
                Update Category
            </button>

            <a href="{{ route('categories.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection