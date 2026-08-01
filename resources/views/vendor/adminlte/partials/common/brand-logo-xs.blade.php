@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'dashboard');

    if (config('adminlte.use_route_url', false)) {
        $dashboard_url = $dashboard_url ? route($dashboard_url) : '';
    } else {
        $dashboard_url = $dashboard_url ? url($dashboard_url) : '';
    }

    $companyName = $company?->company_name ?: config('app.name', 'CCTV Shop Management');

    $logo = $company?->logo
        ? asset('storage/' . ltrim($company->logo, '/'))
        : asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png'));

    $logoAlt = $companyName;
@endphp

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link {{ config('adminlte.classes_brand') }}"
    @endif>

    <img src="{{ $logo }}"
         alt="{{ $logoAlt }}"
         class="{{ config('adminlte.logo_img_class', 'brand-image img-circle elevation-3') }}"
         style="opacity:.9">

    <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
        {{ $companyName }}
    </span>

</a>