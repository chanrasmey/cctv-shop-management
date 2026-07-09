@extends('adminlte::page')

@section('title', 'Edit Product')

@section('content_header')
    <h1>Edit Product</h1>
@stop

@section('content')

<div class="card card-primary">

    <div class="card-header">
        <h3 class="card-title">Edit Product</h3>
    </div>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT')

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id)==$category->id ? 'selected':'' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" class="form-control" required>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id',$product->brand_id)==$brand->id ? 'selected':'' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control" required>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id',$product->unit_id)==$unit->id ? 'selected':'' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id',$product->supplier_id)==$supplier->id ? 'selected':'' }}>
                                    {{ $supplier->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-md-4">
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text"
                               name="sku"
                               class="form-control"
                               value="{{ old('sku',$product->sku) }}"
                               required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Barcode</label>
                        <input type="text"
                               name="barcode"
                               class="form-control"
                               value="{{ old('barcode',$product->barcode) }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Serial Number</label>
                        <input type="text"
                               name="serial_number"
                               class="form-control"
                               value="{{ old('serial_number',$product->serial_number) }}">
                    </div>
                </div>

            </div>

            <div class="form-group">
                <label>Product Name</label>
                <input type="text"
                       name="product_name"
                       class="form-control"
                       value="{{ old('product_name',$product->product_name) }}"
                       required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea
                    name="description"
                    rows="4"
                    class="form-control">{{ old('description',$product->description) }}</textarea>
            </div>

            <div class="row">

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Buy Price</label>
                        <input type="number"
                               step="0.01"
                               name="buy_price"
                               class="form-control"
                               value="{{ old('buy_price',$product->buy_price) }}"
                               required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sell Price</label>
                        <input type="number"
                               step="0.01"
                               name="sell_price"
                               class="form-control"
                               value="{{ old('sell_price',$product->sell_price) }}"
                               required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number"
                               name="stock"
                               class="form-control"
                               value="{{ old('stock',$product->stock) }}"
                               required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Minimum Stock</label>
                        <input type="number"
                               name="minimum_stock"
                               class="form-control"
                               value="{{ old('minimum_stock',$product->minimum_stock) }}"
                               required>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <label>Status</label>

                        <select name="status" class="form-control">

                            <option value="1"
                                {{ old('status',$product->status)==1 ? 'selected':'' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('status',$product->status)==0 ? 'selected':'' }}>
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="form-group">

                        <label>Product Image</label>

                        <input
                            type="file"
                            name="image"
                            class="form-control">

                    </div>

                </div>

            </div>

            @if($product->image)

                <div class="mb-3">

                    <img src="{{ asset('storage/'.$product->image) }}"
                         width="180"
                         class="img-thumbnail">

                </div>

            @endif

        </div>

        <div class="card-footer">

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Product
            </button>

            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                Cancel
            </a>

        </div>

    </form>

</div>

@stop