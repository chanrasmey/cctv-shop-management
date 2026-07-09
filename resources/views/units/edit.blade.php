@extends('layouts.admin')

@section('page_title', 'Edit Unit')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Edit Unit</h3>
    </div>

    <form action="{{ route('units.update',$unit) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Short Name</label>

                        <input
                            type="text"
                            name="short_name"
                            class="form-control"
                            value="{{ old('short_name',$unit->short_name) }}">

                        @error('short_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Unit Name</label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name',$unit->name) }}">

                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

            </div>

            <div class="form-group">

                <label>Status</label>

                <select name="status" class="form-control">

                    <option value="1" {{ $unit->status ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0" {{ !$unit->status ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-success">

                <i class="fas fa-save"></i>

                Update Unit

            </button>

            <a href="{{ route('units.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection