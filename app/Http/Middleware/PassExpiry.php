<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PassExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expiry = auth()->user()->pass_expiry_date;
        $today = Carbon::today();

        if (($today == $expiry) || ($today > $expiry)) {
            return redirect('login');
        }

        return $next($request);
    }
}
