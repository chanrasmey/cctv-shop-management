@extends('layouts.admin')

@section('page_title', 'Create Product')

@section('content_body')

<div class="card">

    <div class="card-header">
        <h3 class="card-title">Create Product</h3>
    </div>

    <form action="{{ route('products.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        <div class="card-body">

            <div class="row">

                {{-- LEFT COLUMN --}}
                <div class="col-md-6">

                    <div class="form-group">
                        <label>SKU *</label>
                        <input type="text"
                               name="sku"
                               class="form-control"
                               value="{{ old('sku') }}">
                        @error('sku')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Barcode</label>
                        <input type="text"
                               name="barcode"
                               class="form-control"
                               value="{{ old('barcode') }}">
                    </div>

                    <div class="form-group">
                        <label>Serial Number</label>
                        <input type="text"
                               name="serial_number"
                               class="form-control"
                               value="{{ old('serial_number') }}">
                    </div>

                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text"
                               name="product_name"
                               class="form-control"
                               value="{{ old('product_name') }}">
                        @error('product_name')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Category *</label>

                        <select name="category_id" class="form-control">

                            <option value="">Select Category</option>

                            @foreach($categories as $category)

                                <option value="{{ $category->id }}"
                                    {{ old('category_id')==$category->id?'selected':'' }}>

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="form-group">
                        <label>Brand *</label>

                        <select name="brand_id" class="form-control">

                            <option value="">Select Brand</option>

                            @foreach($brands as $brand)

                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id')==$brand->id?'selected':'' }}>

                                    {{ $brand->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="form-group">
                        <label>Unit *</label>

                        <select name="unit_id" class="form-control">

                            <option value="">Select Unit</option>

                            @foreach($units as $unit)

                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id')==$unit->id?'selected':'' }}>

                                    {{ $unit->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-md-6">

                    <div class="form-group">

                        <label>Supplier *</label>

                        <select name="supplier_id"
                                class="form-control">

                            <option value="">Select Supplier</option>

                            @foreach($suppliers as $supplier)

                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id')==$supplier->id?'selected':'' }}>

                                    {{ $supplier->company_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="form-group">

                        <label>Buy Price *</label>

                        <input type="number"
                               step="0.01"
                               name="buy_price"
                               class="form-control"
                               value="{{ old('buy_price',0) }}">

                    </div>

                    <div class="form-group">

                        <label>Sell Price *</label>

                        <input type="number"
                               step="0.01"
                               name="sell_price"
                               class="form-control"
                               value="{{ old('sell_price',0) }}">

                    </div>

                    <div class="form-group">

                        <label>Minimum Stock *</label>

                        <input type="number"
                               name="minimum_stock"
                               class="form-control"
                               value="{{ old('minimum_stock',0) }}">

                    </div>

                    <div class="form-group">

                        <label>Opening Stock *</label>

                        <input type="number"
                               name="stock"
                               class="form-control"
                               value="{{ old('stock',0) }}">

                    </div>

                    <div class="form-group">

                        <label>Product Image</label>

                        <input type="file"
                               name="image"
                               class="form-control">

                    </div>

                    <div class="form-group">

                        <label>Status</label>

                        <select name="status"
                                class="form-control">

                            <option value="1">Active</option>

                            <option value="0">Inactive</option>

                        </select>

                    </div>

                </div>

            </div>

            <div class="form-group">

                <label>Description</label>

                <textarea name="description"
                          rows="4"
                          class="form-control">{{ old('description') }}</textarea>

            </div>

        </div>

        <div class="card-footer">

            <button class="btn btn-primary">

                <i class="fas fa-save"></i>

                Save Product

            </button>

            <a href="{{ route('products.index') }}"
               class="btn btn-secondary">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection