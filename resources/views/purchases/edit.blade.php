@extends('layouts.admin')

@section('page_title', 'Edit Purchase')

@section('content_body')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">
            &times;
        </button>

        {{ session('error') }}
    </div>
@endif

<form
    action="{{ route('purchases.update', $purchase) }}"
    method="POST">

    @csrf
    @method('PUT')

    @include('purchases.partials.header')
    @include('purchases.partials.items')
    @include('purchases.partials.totals')

</form>

@endsection

@section('js')
    @include('purchases.partials.scripts')
@endsection
