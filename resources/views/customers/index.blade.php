@extends('layouts.admin')

@section('page_title', 'Customer Management')

@section('content_body')

<div class="card">

    <div class="card-header">

        <h3 class="card-title">Customer List</h3>

        <div class="card-tools">

            <form method="GET" action="{{ route('customers.index') }}" class="d-flex">

                <input type="text"
                       name="search"
                       class="form-control form-control-sm mr-2"
                       placeholder="Search customer..."
                       value="{{ request('search') }}">

                <button class="btn btn-info btn-sm mr-2">
                    <i class="fas fa-search"></i>
                </button>

                <a href="{{ route('customers.create') }}"
                   class="btn btn-primary btn-sm">

                    <i class="fas fa-plus"></i>

                    Add Customer

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
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Opening Balance</th>
                    <th>Status</th>
                    <th width="130">Action</th>

                </tr>

            </thead>

            <tbody>

            @forelse($customers as $customer)

                <tr>

                    <td>{{ $customer->customer_code }}</td>

                    <td>{{ $customer->name }}</td>

                    <td>{{ $customer->phone }}</td>

                    <td>${{ number_format($customer->opening_balance,2) }}</td>

                    <td>

                        @if($customer->status)

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

                        <a href="{{ route('customers.edit',$customer) }}"
                           class="btn btn-warning btn-sm">

                            <i class="fas fa-edit"></i>

                        </a>

                        <form action="{{ route('customers.destroy',$customer) }}"
                              method="POST"
                              style="display:inline">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete customer?')">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="6" class="text-center">

                        No customers found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

        {{ $customers->withQueryString()->links() }}

    </div>

</div>

@endsection