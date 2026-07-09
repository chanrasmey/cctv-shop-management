@extends('layouts.admin')

@section('page_title', 'Supplier Management')

@section('content_body')

<div class="card">

    <div class="card-header">

        <h3 class="card-title">Supplier List</h3>

        <div class="card-tools">

            <form method="GET" action="{{ route('suppliers.index') }}" class="d-flex">

                <input type="text"
                       name="search"
                       class="form-control form-control-sm mr-2"
                       placeholder="Search supplier..."
                       value="{{ request('search') }}">

                <button class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-search"></i>
                </button>

                <a href="{{ route('suppliers.create') }}"
                   class="btn btn-primary btn-sm">

                    <i class="fas fa-plus"></i>
                    Add Supplier

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

                <th>Code</th>
                <th>Company</th>
                <th>Contact</th>
                <th>Phone</th>
                <th>Balance</th>
                <th>Status</th>
                <th width="130">Action</th>

            </tr>

            </thead>

            <tbody>

            @forelse($suppliers as $supplier)

                <tr>

                    <td>{{ $supplier->supplier_code }}</td>

                    <td>{{ $supplier->company_name }}</td>

                    <td>{{ $supplier->contact_person }}</td>

                    <td>{{ $supplier->phone }}</td>

                    <td>${{ number_format($supplier->opening_balance,2) }}</td>

                    <td>

                        @if($supplier->status)

                            <span class="badge badge-success">Active</span>

                        @else

                            <span class="badge badge-danger">Inactive</span>

                        @endif

                    </td>

                    <td>

                        <a href="{{ route('suppliers.edit',$supplier) }}"
                           class="btn btn-warning btn-sm">

                            <i class="fas fa-edit"></i>

                        </a>

                        <form action="{{ route('suppliers.destroy',$supplier) }}"
                              method="POST"
                              style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete supplier?')">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center">

                        No suppliers found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        {{ $suppliers->withQueryString()->links() }}

    </div>

</div>

@endsection