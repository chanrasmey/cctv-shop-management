@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content_body')

<div class="row">

    <div class="col-lg-2 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $products }}</h3>
                <p>Products</p>
            </div>
            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $categories }}</h3>
                <p>Categories</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-2 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $brands }}</h3>
                <p>Brands</p>
            </div>
            <div class="icon">
                <i class="fas fa-copyright"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $customers }}</h3>
                <p>Customers</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $suppliers }}</h3>
                <p>Suppliers</p>
            </div>
            <div class="icon">
                <i class="fas fa-truck"></i>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Latest Categories</h3>
            </div>

            <div class="card-body">

                <table class="table table-sm">

                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($latestCategories as $category)

                        <tr>
                            <td>{{ $category->code }}</td>
                            <td>{{ $category->name }}</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="2">No categories found.</td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <div class="col-md-6">

        <div class="card">

            <div class="card-header">
                <h3 class="card-title">Latest Brands</h3>
            </div>

            <div class="card-body">

                <table class="table table-sm">

                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($latestBrands as $brand)

                        <tr>
                            <td>{{ $brand->code }}</td>
                            <td>{{ $brand->name }}</td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="2">No brands found.</td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection