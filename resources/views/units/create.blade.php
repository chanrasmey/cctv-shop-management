@extends('layouts.admin')

@section('page_title', 'Create Unit')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Create Unit</h3>
    </div>

    <form action="{{ route('units.store') }}" method="POST">

        @csrf

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Short Name <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="short_name"
                            class="form-control"
                            value="{{ old('short_name') }}"
                            placeholder="PCS">

                        <small class="text-muted">
                            Example: PCS, BOX, SET, ROLL, METER
                        </small>

                        @error('short_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Unit Name <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            placeholder="Piece">

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

                Save Unit

            </button>

            <a href="{{ route('units.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection