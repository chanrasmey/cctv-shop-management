@extends('layouts.admin')

@section('page_title', 'Brand Management')

@section('content_body')

<div class="card">

    <div class="card-header">

        <h3 class="card-title">Brand List</h3>

        <div class="card-tools">

            <form method="GET" action="{{ route('brands.index') }}" class="d-flex">

                <input type="text"
                       name="search"
                       class="form-control form-control-sm mr-2"
                       placeholder="Search..."
                       value="{{ request('search') }}">

                <button class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-search"></i>
                </button>

                <a href="{{ route('brands.create') }}"
                   class="btn btn-primary btn-sm">

                    <i class="fas fa-plus"></i>
                    Add Brand

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
                    <th width="80">ID</th>
                    <th width="150">Code</th>
                    <th>Name</th>
                    <th width="120">Status</th>
                    <th width="140">Action</th>
                </tr>

            </thead>

            <tbody>

            @forelse($brands as $brand)

                <tr>

                    <td>{{ $brand->id }}</td>

                    <td>{{ $brand->code }}</td>

                    <td>{{ $brand->name }}</td>

                    <td>

                        @if($brand->status)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif

                    </td>

                    <td>

                        <a href="{{ route('brands.edit',$brand) }}"
                           class="btn btn-warning btn-sm">

                            <i class="fas fa-edit"></i>

                        </a>

                        <form action="{{ route('brands.destroy',$brand) }}"
                              method="POST"
                              style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this brand?')">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" class="text-center">

                        No brands found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        {{ $brands->withQueryString()->links() }}

    </div>

</div>

@endsection