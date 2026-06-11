<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|array  $roles
     * @param  string  $requireAll  - 'all' jika harus punya semua roles, 'any' jika boleh salah satu
     */
    public function handle(Request $request, Closure $next, $roles, $requireAll = 'any'): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Superuser has all permissions
        if ($user->isSuperuser()) {
            return $next($request);
        }

        $roleArray = explode('|', $roles);
        $activeMosque = $user->getActiveMosque();

        if (! $activeMosque) {
            return response()->json(['message' => 'No active mosque selected'], 403);
        }

        $hasAccess = $requireAll === 'all'
            ? $user->hasAllRolesInMosque($roleArray, $activeMosque->id)
            : $user->hasAnyRoleInMosque($roleArray, $activeMosque->id);

        if (! $hasAccess) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
