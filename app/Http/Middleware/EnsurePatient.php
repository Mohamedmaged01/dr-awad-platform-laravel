<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the patient portal dashboard: requires an authenticated patient account.
 */
class EnsurePatient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'patient' || ! $user->is_active) {
            return redirect()->guest('/patient-portal');
        }

        return $next($request);
    }
}
