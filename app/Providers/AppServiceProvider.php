<?php

namespace App\Providers;

use App\Models\County;
use App\Models\Constituency;
use App\Models\Ward;
use App\Models\Location;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Catalogue;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Check if the database connection works
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            die('Database does not exist or connection failed.');
        }

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
        ]);
    }
}
