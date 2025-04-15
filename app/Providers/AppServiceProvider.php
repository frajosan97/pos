<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\Permission;
use App\Models\Catalogue;
use App\Models\Company;
use App\Models\Constituency;
use App\Models\County;
use App\Models\Location;
use App\Models\Products;
use App\Models\User;
use App\Models\Ward;
use App\Services\RoleFetchService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
    * Register any application services.
    */

    public function register(): void {
        // Register application services, if needed.
    }

    /**
    * Bootstrap any application services.
    */

    public function boot( RoleFetchService $roleFetchService ): void {
        // // Get fetchType data using the service
        $fetchTypeData = $roleFetchService->getFetchData();

        // Share data with views
        view()->share( array_merge( $fetchTypeData, [
            'company_info' => Company::first(),
            'counties' => County::all(),
            'constituencies' => Constituency::all(),
            'wards' => Ward::all(),
            'locations' => Location::all(),
            'branches' => Branch::all(),
            'permissions' => Permission::all(),
            'catalogue' => Catalogue::all(),
            'products' => Products::all(),
            'employees' => User::all(),
        ] ) );
    }
}
