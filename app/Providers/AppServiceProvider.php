<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\Catalogue;
use App\Models\Company;
use App\Models\Constituency;
use App\Models\County;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register application services, if needed.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Proceed with sharing data if checks pass
        view()->share([
            'company_info' => Company::first(),
            'roles' => Role::all(),
            'counties' => County::all(),
            'constituencies' => Constituency::all(),
            'wards' => Ward::all(),
            'locations' => Location::all(),
            'branches' => Branch::all(),
            'catalogue' => Catalogue::all(),
            'employees' => User::all(),
        ]);
    }
}
