@extends('adminlte::master')

@php
    $authType = $authType ?? 'login';

    $dashboardUrl = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'dashboard');

    if (config('adminlte.use_route_url', false)) {
        $dashboardUrl = $dashboardUrl ? route($dashboardUrl) : '';
    } else {
        $dashboardUrl = $dashboardUrl ? url($dashboardUrl) : '';
    }

    $bodyClasses = "{$authType}-page";

    if (! empty(config('adminlte.layout_dark_mode'))) {
        $bodyClasses .= ' dark-mode';
    }

    $companyName = $company?->company_name ?: config('app.name', 'CCTV Shop Management');
    $companyPhone = $company?->phone;
    $companyAddress = $company?->address;

    $logo = $company?->logo
        ? asset('storage/' . ltrim($company->logo, '/'))
        : asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png'));
@endphp

@section('adminlte_css')
    @stack('css')
    @yield('css')

    <style>
        .login-logo img,
        .register-logo img {
            max-height: 90px;
            margin-bottom: 12px;
        }

        .login-logo,
        .register-logo {
            text-align: center;
        }

        .login-logo a,
        .register-logo a {
            color: #343a40;
            text-decoration: none;
        }

        .company-address {
            font-size: .85rem;
            color: #6c757d;
            margin-top: 4px;
        }

        .company-phone {
            font-size: .85rem;
            color: #6c757d;
        }
    </style>
@stop

@section('classes_body')
{{ $bodyClasses }}
@stop

@section('body')

<div class="{{ $authType }}-box">

    <div class="{{ $authType }}-logo">

        <a href="{{ $dashboardUrl }}">

            <img src="{{ $logo }}"
                 alt="{{ $companyName }}">

            <div>
                <strong>{{ $companyName }}</strong>
            </div>

            @if($companyAddress)
                <div class="company-address">
                    {{ $companyAddress }}
                </div>
            @endif

            @if($companyPhone)
                <div class="company-phone">
                    {{ $companyPhone }}
                </div>
            @endif

        </a>

    </div>

    <div class="card {{ config('adminlte.classes_auth_card', 'card-outline card-primary') }}">

        @hasSection('auth_header')
            <div class="card-header {{ config('adminlte.classes_auth_header') }}">
                <h3 class="card-title float-none text-center">
                    @yield('auth_header')
                </h3>
            </div>
        @endif

        <div class="card-body {{ $authType }}-card-body {{ config('adminlte.classes_auth_body') }}">
            @yield('auth_body')
        </div>

        @hasSection('auth_footer')
            <div class="card-footer {{ config('adminlte.classes_auth_footer') }}">
                @yield('auth_footer')
            </div>
        @endif

    </div>

</div>

@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop