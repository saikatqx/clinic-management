<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (! $user || ($user->role ?? 'user') !== $role) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
