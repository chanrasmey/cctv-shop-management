@extends('layouts.admin')

@section('page_title', 'Unit Management')

@section('content_body')

<div class="card">

    <div class="card-header">

        <h3 class="card-title">Unit List</h3>

        <div class="card-tools">

            <form method="GET" action="{{ route('units.index') }}" class="d-flex">

                <input type="text"
                       name="search"
                       class="form-control form-control-sm mr-2"
                       placeholder="Search Unit..."
                       value="{{ request('search') }}">

                <button class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-search"></i>
                </button>

                <a href="{{ route('units.create') }}"
                   class="btn btn-primary btn-sm">

                    <i class="fas fa-plus"></i>
                    Add Unit

                </a>

            </form>

        </div>

    </div>

    <div class="card-body table-responsive">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th width="70">ID</th>
                    <th width="150">Short Name</th>
                    <th>Unit Name</th>
                    <th width="120">Status</th>
                    <th width="140">Action</th>
                </tr>
            </thead>

            <tbody>

            @forelse($units as $unit)

                <tr>

                    <td>{{ $unit->id }}</td>

                    <td>
                        <strong>{{ $unit->short_name }}</strong>
                    </td>

                    <td>{{ $unit->name }}</td>

                    <td>

                        @if($unit->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif

                    </td>

                    <td>

                        <a href="{{ route('units.edit',$unit) }}"
                           class="btn btn-warning btn-sm">

                            <i class="fas fa-edit"></i>

                        </a>

                        <form action="{{ route('units.destroy',$unit) }}"
                              method="POST"
                              style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this unit?')">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" class="text-center">

                        No units found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        {{ $units->withQueryString()->links() }}

    </div>

</div>

@endsection