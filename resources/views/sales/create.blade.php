@extends('adminlte::page')

@section('title', 'Create Sale')

@section('content_header')

<div class="d-flex justify-content-between align-items-center">

    <h1>

        <i class="fas fa-cash-register text-success mr-2"></i>

        Create Sale

    </h1>

    <a href="{{ route('sales.index') }}"
       class="btn btn-secondary">

        <i class="fas fa-arrow-left mr-1"></i>

        Back

    </a>

</div>

@stop

@section('content')

<form action="{{ route('sales.store') }}"
      method="POST"
      id="sale-form"
      autocomplete="off">

    @csrf

    <div class="row">

        {{-- Left Column --}}
        <div class="col-lg-9">

            @include('sales.partials.customer')

            @include('sales.partials.items')

        </div>

        {{-- Right Column --}}
        <div class="col-lg-3">

            @include('sales.partials.totals')

            @include('sales.partials.payment')

        </div>

    </div>
        {{-- ==========================================================
    | Action Buttons
    ========================================================== --}}

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-body text-right">

                    <a href="{{ route('sales.index') }}"
                       class="btn btn-secondary">

                        <i class="fas fa-times mr-1"></i>

                        Cancel

                    </a>

                    <button type="reset"
                            class="btn btn-warning ml-2">

                        <i class="fas fa-undo mr-1"></i>

                        Reset

                    </button>

                    <button type="submit"
                            id="btn-save-sale"
                            class="btn btn-success ml-2">

                        <i class="fas fa-save mr-1"></i>

                        Save Sale

                    </button>

                </div>

            </div>

        </div>

    </div>

</form>

@endsection

@push('css')

<style>

#sale-items-table input[type="number"]{

    min-width:90px;

}

#sale-items-table td{

    vertical-align:middle;

}

#grand_total_display{

    font-size:1.35rem;

    font-weight:bold;

}

#paid_amount{

    font-size:1.15rem;

    font-weight:bold;

}

.select2-container{

    width:100%!important;

}

</style>

@endpush

@include('sales.partials.scripts')