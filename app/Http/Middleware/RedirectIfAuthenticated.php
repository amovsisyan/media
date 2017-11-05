<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $route = 'category'; //welcome user page
        if ($guard === 'admin') {
            $route = 'adminDashboard';
        }
        if (Auth::guard($guard)->check()) {
            return redirect()->route($route, ['locale' => \App::getLocale()]);
        }

        return $next($request);
    }
}
