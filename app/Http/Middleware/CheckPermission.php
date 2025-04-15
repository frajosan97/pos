<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckPermission {
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */

    public function handle( $request, Closure $next ) {
        // Ensure there is a valid route before attempting to get its name
        $routeName = Route::currentRouteName();
        // Safely retrieve the current route name

        if ( $routeName ) {
            // create the permission slug
            $permissionSlug = permissionSlug($routeName);
            
            // Check if the user is authenticated and has the required permission
            if ( !Auth::check() || !Auth::user()->hasPermission( $permissionSlug ) ) {
                abort( 403, 'sorry but you do not have permission to access this page' );
            }
        }

        return $next( $request );
    }
}

