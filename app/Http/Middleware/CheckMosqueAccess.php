<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMosqueAccess
{
    /**
     * Handle an incoming request.
     * Verifikasi bahwa user punya akses ke mosque yang diminta
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Superuser dapat akses semua masjid
        if ($user->isSuperuser()) {
            return $next($request);
        }

        // Check apakah ada mosque_id di URL params
        $mosqueId = $request->route('mosque')
            ?: $request->query('mosque_id')
            ?: $request->input('mosque_id');

        if ($mosqueId && ! $user->mosques()->where('mosques.id', $mosqueId)->exists()) {
            return response()->json(['message' => 'Unauthorized to access this mosque'], 403);
        }

        return $next($request);
    }
}
