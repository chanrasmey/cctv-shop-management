@if(session('success'))

<div class="alert alert-success alert-dismissible fade show">

    <i class="fas fa-check-circle mr-2"></i>

    {{ session('success') }}

    <button type="button"
            class="close"
            data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif

@if(session('error'))

<div class="alert alert-danger alert-dismissible fade show">

    <i class="fas fa-times-circle mr-2"></i>

    {{ session('error') }}

    <button type="button"
            class="close"
            data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif

@if(session('warning'))

<div class="alert alert-warning alert-dismissible fade show">

    <i class="fas fa-exclamation-triangle mr-2"></i>

    {{ session('warning') }}

    <button type="button"
            class="close"
            data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif

@if(session('info'))

<div class="alert alert-info alert-dismissible fade show">

    <i class="fas fa-info-circle mr-2"></i>

    {{ session('info') }}

    <button type="button"
            class="close"
            data-dismiss="alert">

        <span>&times;</span>

    </button>

</div>

@endif

@if($errors->any())

<div class="alert alert-danger">

    <i class="fas fa-times-circle mr-2"></i>

    <strong>Please correct the following:</strong>

    <ul class="mb-0 mt-2">

        @foreach($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif