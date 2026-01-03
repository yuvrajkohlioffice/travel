<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\RoleRoute;
use Illuminate\Support\Str;

class CheckRoleRoute
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // ❌ Guest or user without role
        if (!$user || !$user->role_id) {
            return $this->deny($request);
        }

        // ✅ Super Admin (role_id = 1) → allow all
        if ((int) $user->role_id === 1) {
            return $next($request);
        }

        $currentRouteName = Route::currentRouteName();

        // ✅ Routes without name → allow
        if (!$currentRouteName) {
            return $next($request);
        }

        // Cache allowed routes per role
        $allowedRoutes = cache()->remember(
            "role_routes_{$user->role_id}",
            now()->addMinutes(10),
            fn () => RoleRoute::where('role_id', $user->role_id)
                ->pluck('route_name')
                ->toArray()
        );

        // 🔒 Permission check (supports wildcards)
        foreach ($allowedRoutes as $allowed) {
            if (Str::is($allowed, $currentRouteName)) {
                return $next($request); // ✅ allowed → NO LOG
            }
        }

        // ❌ Not allowed → redirect back with error
        return $this->deny($request);
    }

    /**
     * Handle denied access
     */
    protected function deny(Request $request)
    {
        // For API / AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Access Denied! Ask Admin for permission.'
            ], 403);
        }

        // For web requests
        return redirect()
            ->back()
            ->with('error', 'Access Denied! Ask Admin for permission.');
    }
}
