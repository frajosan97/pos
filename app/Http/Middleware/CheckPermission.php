<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        // If the user is not authenticated or does not have the required permission, abort with a 403 error.
        if (!Auth::check() || !Auth::user()->hasPermission($permission)) {
            return abort(403, 'Unauthorized');
        }

        // Proceed to the next middleware/request if authorized
        return $next($request);
    }
}
