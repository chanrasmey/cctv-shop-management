@extends('layouts.admin')

@section('page_title', 'Purchase Return')

@section('content_body')

<form
    action="{{ route('purchases.returns.store', $purchase) }}"
    method="POST">

    @csrf

    @include('purchase-returns.partials.header')

    @include('purchase-returns.partials.items')

    @include('purchase-returns.partials.totals')

</form>

@endsection

@section('js')

    @include('purchase-returns.partials.scripts')

@endsection