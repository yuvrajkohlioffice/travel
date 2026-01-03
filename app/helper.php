<?php

if (!function_exists('canRoute')) {
    function canRoute(string $routeName): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // ✅ role_id = 1 (Super Admin) → allow everything
        if ($user->role_id === 1) {
            return true;
        }

        // 🔒 Other roles → permission based
        $allowedRoutes = cache()->remember(
            "role_routes_{$user->role_id}",
            now()->addMinutes(10),
            fn () => \App\Models\RoleRoute::where('role_id', $user->role_id)
                ->pluck('route_name')
                ->toArray()
        );

        foreach ($allowedRoutes as $allowed) {
            if (\Illuminate\Support\Str::is($allowed, $routeName)) {
                return true;
            }
        }

        return false;
    }
}
