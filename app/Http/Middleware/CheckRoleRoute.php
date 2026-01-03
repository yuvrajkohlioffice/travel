<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\RoleRoute;

class CheckRoleRoute
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        // ❌ Guest or invalid user
        if (!$user || !$user->role_id) {
            \Log::warning('403: Guest or user without role tried to access ' . Route::currentRouteName());
            abort(403, 'Access Denied');
        }

        // ✅ role_id = 1 (Super Admin) → allow everything
        if ((int) $user->role_id === 1) {
            \Log::info("✅ Super Admin {$user->id} accessed " . Route::currentRouteName());
            return $next($request);
        }

        $currentRouteName = Route::currentRouteName();

        // Routes without names are allowed
        if (!$currentRouteName) {
            \Log::info("ℹ️ Route without name accessed by user {$user->id}");
            return $next($request);
        }

        $allowedRoutes = cache()->remember(
            "role_routes_{$user->role_id}",
            now()->addMinutes(10),
            fn () => RoleRoute::where('role_id', $user->role_id)
                ->pluck('route_name')
                ->toArray()
        );

        // 🔒 Permission check (supports wildcards)
        foreach ($allowedRoutes as $allowed) {
            if (\Illuminate\Support\Str::is($allowed, $currentRouteName)) {
                \Log::info("✅ User {$user->id} accessed {$currentRouteName}");
                return $next($request);
            }
        }

        \Log::warning(
            "403: User {$user->id} (role_id: {$user->role_id}) tried {$currentRouteName}. " .
            "Allowed: " . implode(', ', $allowedRoutes)
        );

        abort(403, 'Access Denied! Ask Admin For Permission.');
    }
}
