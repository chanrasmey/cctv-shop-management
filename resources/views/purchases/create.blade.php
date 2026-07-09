@extends('layouts.admin')

@section('page_title', 'Create Purchase')

@section('content_body')

<form action="{{ route('purchases.store') }}" method="POST">

    @csrf

    @include('purchases.partials.header')

    @include('purchases.partials.items')

    @include('purchases.partials.totals')

</form>

@endsection

@section('js')

    @include('purchases.partials.scripts')

@endsection