@extends('layouts.admin')

@section('page_title', 'Category Management')

@section('content_body')

<div class="card">

    <div class="card-header">

        <h3 class="card-title">
            Category List
        </h3>

        <div class="card-tools">

            <form method="GET" action="{{ route('categories.index') }}" class="d-flex">

                <input type="text"
                       name="search"
                       class="form-control form-control-sm mr-2"
                       placeholder="Search..."
                       value="{{ request('search') }}">

                <button class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-search"></i>
                </button>

                <a href="{{ route('categories.create') }}"
                   class="btn btn-primary btn-sm">

                    <i class="fas fa-plus"></i>
                    Add Category

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

            <thead class="thead-light">

                <tr>

                    <th width="70">ID</th>
                    <th width="120">Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th width="120">Status</th>
                    <th width="140">Action</th>

                </tr>

            </thead>

            <tbody>

            @forelse($categories as $category)

                <tr>

                    <td>{{ $category->id }}</td>

                    <td>{{ $category->code }}</td>

                    <td>{{ $category->name }}</td>

                    <td>{{ $category->description }}</td>

                    <td>

                        @if($category->status)

                            <span class="badge badge-success">

                                Active

                            </span>

                        @else

                            <span class="badge badge-danger">

                                Inactive

                            </span>

                        @endif

                    </td>

                    <td>

                        <a href="{{ route('categories.edit',$category) }}"
                           class="btn btn-warning btn-sm">

                            <i class="fas fa-edit"></i>

                        </a>

                        <form action="{{ route('categories.destroy',$category) }}"
                              method="POST"
                              style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this category?')">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" class="text-center">

                        No category found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        {{ $categories->withQueryString()->links() }}

    </div>

</div>

@endsection