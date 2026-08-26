<?php

namespace App\Http\Middleware;

use App\Support\Access;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the admin area: requires an authenticated, active, non-patient user.
 * Optional role arguments restrict a route further, e.g. `staff:admin,doctor`.
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();

        if (! $user || $user->role === 'patient' || ! $user->is_active) {
            return redirect()->guest(route('admin.login'));
        }

        // Explicit role list on a route (kept for any hard-coded guards).
        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            abort(403);
        }

        // Dynamic feature permissions (admin-managed matrix). Admin bypasses.
        if ($user->role !== 'admin') {
            $feature = Access::featureForPath($request->path());
            if ($feature && ! Access::allows($user->role, $feature)) {
                abort(403);
            }
        }

        return $next($request);
    }
}
