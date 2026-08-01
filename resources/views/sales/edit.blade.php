@extends('adminlte::page')

@section('title', 'Edit Sale')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1 class="m-0">

        <i class="fas fa-edit mr-2"></i>

        Edit Sale

    </h1>

    <div>

        <a
            href="{{ route('sales.show',$sale) }}"
            class="btn btn-info">

            <i class="fas fa-eye"></i>

            View

        </a>

        <a
            href="{{ route('sales.index') }}"
            class="btn btn-secondary">

            <i class="fas fa-arrow-left"></i>

            Back

        </a>

    </div>

</div>

@stop

@section('content')

@if($errors->any())

<div class="alert alert-danger">

    <h5>

        <i class="fas fa-ban"></i>

        Please fix the following errors

    </h5>

    <ul class="mb-0">

        @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif

<form
    action="{{ route('sales.update',$sale) }}"
    method="POST"
    id="sale-form"
    autocomplete="off">

    @csrf
    @method('PUT')

    <div class="card card-outline card-info">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-receipt mr-1"></i>
            Sale Information
        </h3>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3">

                <div class="form-group">

                    <label>Sale Date</label>

                    <input
                        type="date"
                        name="sale_date"
                        class="form-control"
                        value="{{ old('sale_date', \Illuminate\Support\Carbon::parse($sale->sale_date)->toDateString()) }}"
                        required>

                </div>

            </div>

            <div class="col-md-3">

                <div class="form-group">

                    <label>Status</label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $sale->status }}"
                        readonly>

                </div>

            </div>

        </div>

    </div>

</div>
    <div class="row">

        <div class="col-md-12">

            @include('sales.partials.customer')

        </div>

    </div>

    <div class="row">

        <div class="col-md-12">

            @include('sales.partials.items')

        </div>

    </div>

    <div class="row">

        <div class="col-md-12">

            @include('sales.partials.summary')

        </div>

    </div>

    <div class="card">

        <div class="card-body">

            <div class="text-right">

                <a
                    href="{{ route('sales.index') }}"
                    class="btn btn-secondary">

                    <i class="fas fa-times"></i>

                    Cancel

                </a>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fas fa-save"></i>

                    Update Sale

                </button>

            </div>

        </div>

    </div>

</form>

@stop

@section('plugins.Select2', true)

@section('plugins.Sweetalert2', true)

@push('css')

<style>

.select2-container--default .select2-selection--single{

    height:38px;

}

.select2-container--default .select2-selection--single .select2-selection__rendered{

    line-height:36px;

}

.select2-container--default .select2-selection--single .select2-selection__arrow{

    height:36px;

}

</style>

@endpush

@push('js')

<script>

$(function(){

    $('.select2').select2({

        width:'100%',

        placeholder:'Select',

        allowClear:true

    });

    $('#sale-form').submit(function(){

        if($('#sale-items-body tr').length===0){

            Swal.fire({

                icon:'warning',

                title:'No Item',

                text:'Please add at least one product.'

            });

            return false;

        }

        $(this)

            .find('button[type=submit]')

            .prop('disabled',true)

            .html('<i class="fas fa-spinner fa-spin"></i> Updating...');

    });

});

</script>

@endpush