@extends('layouts.admin')

@section('page_title', 'Product Details')

@section('content_body')

<div class="row">

    <div class="col-md-4">

        <div class="card card-primary">

            <div class="card-header">
                <h3 class="card-title">Product Image</h3>
            </div>

            <div class="card-body text-center">

             @if($product->image)

    <img src="{{ asset('storage/'.$product->image) }}"
         class="img-fluid img-thumbnail"
         style="max-height:300px; cursor:pointer;"
         data-toggle="modal"
         data-target="#imageModal">

@else

    <img src="https://via.placeholder.com/300x300?text=No+Image"
         class="img-fluid img-thumbnail">

@endif

            </div>

        </div>

    </div>

    <div class="col-md-8">

        <div class="card card-primary">

            <div class="card-header">
                <h3 class="card-title">
                    {{ $product->product_name }}
                </h3>
            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="220">SKU</th>
                        <td>{{ $product->sku }}</td>
                    </tr>

                    <tr>
    <th>Barcode</th>
    <td>

        @if($product->barcode)

            <div class="mb-2">
                {!! DNS1D::getBarcodeHTML($product->barcode, 'C128', 2, 60) !!}
            </div>

            <strong>{{ $product->barcode }}</strong>

        @else

            <span class="text-muted">No Barcode</span>

        @endif

    </td>
</tr>

                    <tr>
                        <th>Serial Number</th>
                        <td>{{ $product->serial_number ?: '-' }}</td>
                    </tr>

                    <tr>
                        <th>Category</th>
                        <td>{{ $product->category->name }}</td>
                    </tr>

                    <tr>
                        <th>Brand</th>
                        <td>{{ $product->brand->name }}</td>
                    </tr>

                    <tr>
                        <th>Unit</th>
                        <td>{{ $product->unit->name }}</td>
                    </tr>

                    <tr>
                        <th>Supplier</th>
                        <td>{{ $product->supplier->company_name }}</td>
                    </tr>

                    <tr>
                        <th>Buy Price</th>
                        <td>$ {{ number_format($product->buy_price,2) }}</td>
                    </tr>

                    <tr>
                        <th>Sell Price</th>
                        <td>$ {{ number_format($product->sell_price,2) }}</td>
                    </tr>

                    <tr>
                        <th>Profit</th>
                        <td>
                            <span class="badge badge-success">
                                $ {{ number_format($product->profit,2) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Current Stock</th>
                        <td>

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
                    </tr>

                    <tr>
                        <th>Minimum Stock</th>
                        <td>{{ $product->minimum_stock }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
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
                    </tr>

                    <tr>
                        <th>Description</th>
                        <td>
                            {{ $product->description ?: '-' }}
                        </td>
                    </tr>

                    <tr>
                        <th>Created At</th>
                        <td>{{ $product->created_at->format('d M Y H:i') }}</td>
                    </tr>

                    <tr>
                        <th>Last Updated</th>
                        <td>{{ $product->updated_at->format('d M Y H:i') }}</td>
                    </tr>

                </table>

            </div>

            <div class="card-footer">
               
                <a href="{{ route('products.barcode', $product->id) }}"
                    target="_blank"
                    class="btn btn-success">

                     <i class="fas fa-barcode"></i>

                    Print Barcode

                </a>
                <a href="{{ route('products.index') }}"
                   class="btn btn-secondary">

                    <i class="fas fa-arrow-left"></i>

                    Back

                </a>

                <a href="{{ route('products.edit',$product->id) }}"
                   class="btn btn-warning">

                    <i class="fas fa-edit"></i>

                    Edit

                </a>

            </div>

        </div>

    </div>

</div>
@if($product->image)

<div class="modal fade" id="imageModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    {{ $product->product_name }}
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <div class="modal-body text-center">

                <img src="{{ asset('storage/'.$product->image) }}"
                     class="img-fluid">

            </div>

        </div>

    </div>

</div>

@endif

@endsection