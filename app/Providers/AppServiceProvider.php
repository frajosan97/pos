<?php

namespace App\Providers;

use App\Models\County;
use App\Models\Constituency;
use App\Models\Ward;
use App\Models\Location;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Catalogue;
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
        view()->share([
            'company_info' => [
                'name' => 'Atricare Point of Sale (POS)',
                'address' => '123 Main Street, Nairobi, Kenya',
                'phone' => '0796594366',
                'email' => 'support@atricare.co.ke'
            ],
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
