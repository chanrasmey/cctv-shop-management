@extends('layouts.admin')

@section('page_title', 'Product Management')

@section('content_body')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

<div class="card">

    <div class="card-header">

        <h3 class="card-title">
            Product List
        </h3>

        <div class="card-tools">

            <form action="{{ route('products.index') }}"
                  method="GET"
                  class="d-flex">

                <input type="text"
                       name="search"
                       class="form-control form-control-sm mr-2"
                       placeholder="Search SKU / Barcode / Product"
                       value="{{ request('search') }}">

                <button type="submit"
                        class="btn btn-info btn-sm mr-2">

                    <i class="fas fa-search"></i>

                </button>

                <a href="{{ route('products.create') }}"
                   class="btn btn-primary btn-sm">

                    <i class="fas fa-plus"></i>

                    Add Product

                </a>

            </form>

        </div>

    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-bordered table-hover text-nowrap">

            <thead>

            <tr>

                <th width="80">Image</th>
                <th>SKU</th>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th width="80">Stock</th>
                <th>Buy Price</th>
                <th>Sell Price</th>
                <th>Status</th>
                <th width="150">Action</th>

            </tr>

            </thead>

            <tbody>

            @forelse($products as $product)

                <tr>

                    <td class="text-center">

                        @if($product->image)

                            <img src="{{ asset('storage/'.$product->image) }}"
                                 width="60"
                                 height="60"
                                 class="img-thumbnail">

                        @else

                            <img src="https://via.placeholder.com/60x60?text=No+Image"
                                 width="60"
                                 height="60"
                                 class="img-thumbnail">

                        @endif

                    </td>

                    <td>{{ $product->sku }}</td>

                    <td>

                        <strong>{{ $product->product_name }}</strong>

                        @if($product->barcode)
                            <br>
                            <small class="text-muted">
                                Barcode : {{ $product->barcode }}
                            </small>
                        @endif

                    </td>

                    <td>{{ $product->category->name }}</td>

                    <td>{{ $product->brand->name }}</td>

                    <td>{{ $product->supplier->company_name }}</td>

                    <td class="text-center">

                        @if($product->stock <= $product->minimum_stock)

                            <span class="badge badge-danger">
                                {{ $product->stock }}
                            </span>

                        @else

                            <span class="badge badge-success">
                                {{ $product->stock }}
                            </span>

                        @endif

                    </td>

                    <td>${{ number_format($product->buy_price,2) }}</td>

                    <td>${{ number_format($product->sell_price,2) }}</td>

                    <td>

                        @if($product->status)

                            <span class="badge badge-success">
                                Active
                            </span>

                        @else

                            <span class="badge badge-secondary">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>

    <a href="{{ route('products.show', $product->id) }}"
       class="btn btn-info btn-sm"
       title="View">

        <i class="fas fa-eye"></i>

    </a>

    <a href="{{ route('products.edit', $product->id) }}"
       class="btn btn-warning btn-sm"
       title="Edit">

        <i class="fas fa-edit"></i>

    </a>

    <form action="{{ route('products.destroy', $product->id) }}"
          method="POST"
          style="display:inline-block;"
          onsubmit="return confirm('Are you sure you want to delete this product?');">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger btn-sm"
                title="Delete">

            <i class="fas fa-trash"></i>

        </button>

    </form>

</td>

                </tr>

            @empty

               <tr class="{{ $product->stock <= $product->minimum_stock ? 'table-danger' : '' }}">

                    <td colspan="11" class="text-center">

                        No Products Found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    @if($products->hasPages())

        <div class="card-footer clearfix">

            {{ $products->withQueryString()->links() }}

        </div>

    @endif

</div>

@endsection