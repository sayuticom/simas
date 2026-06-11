<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActiveMosque
{
    public function handle(Request $request, Closure $next)
    {
        // Allow selecting mosque and auth routes (login/logout)
        if ($request->routeIs('mosque.select') || $request->routeIs('login') || $request->routeIs('login.process') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Superuser boleh langsung masuk tanpa memilih masjid
        if (auth()->check() && auth()->user()->isSuperuser()) {
            return $next($request);
        }

        $mosqueId = session('active_mosque_id');
        if (! $mosqueId) {
            return redirect()->route('dashboard', ['choose_mosque' => 1]);
        }

        return $next($request);
    }
}
