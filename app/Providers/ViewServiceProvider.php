<?php

namespace App\Providers;

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {

            static $company = null;

            if ($company !== null) {
                $view->with('company', $company);
                return;
            }

            try {
                if (! Schema::hasTable('company_settings')) {
                    $company = null;
                } else {
                    $company = CompanySetting::query()->first();
                }
            } catch (\Throwable $e) {
                $company = null;
            }

            $view->with('company', $company);
        });
    }
}