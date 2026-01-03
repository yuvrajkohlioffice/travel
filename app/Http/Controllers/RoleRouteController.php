<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleRoute;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RoleRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('routes')->select('id', 'name');

            return DataTables::of($roles)
                ->addIndexColumn()
                ->addColumn('routes', function ($role) {
                    $count = $role->routes->count();
                    if ($count === 0) {
                        return '<span class="inline-block bg-gray-400 text-white px-2 py-1 rounded-full text-xs">0</span>';
                    }
                    return '<span class="inline-block bg-blue-500 text-white px-2 py-1 rounded-full text-xs">' . $count . '</span>';
                })
                ->addColumn('actions', function ($role) {
                    $editUrl = route('role_routes.create', ['role_id' => $role->id]);
                    return '<a href="' .
                        $editUrl .
                        '" class="inline-flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>';
                })
                ->rawColumns(['routes', 'actions'])
                ->make(true);
        }

        return view('role_routes.index');
    }

    /**
     * Show the form for creating/updating role routes.
     */
    public function create(Request $request)
    {
        $roles = Role::all();

        // Get all named routes that are inside admin group & protected by middleware
        $routeCollection = \Illuminate\Support\Facades\Route::getRoutes();

        $routes = collect($routeCollection)
            ->filter(function ($route) {
                $middlewares = $route->middleware();

                return $route->getName() && in_array('auth:sanctum', $middlewares) && in_array(config('jetstream.auth_session'), $middlewares) && in_array('verified', $middlewares);
            })
            ->map(fn($route) => $route->getName())
            ->sort() // ✅ alphabetic order
            ->values();

        // If ?role_id is passed, prefill selected role & routes
        $selectedRole = null;
        $selectedRoutes = [];

        if ($request->has('role_id')) {
            $selectedRole = Role::with('routes')->findOrFail($request->role_id);
            $selectedRoutes = $selectedRole->routes->pluck('route_name')->toArray();
        }

        return view('role_routes.create', compact('roles', 'routes', 'selectedRole', 'selectedRoutes'));
    }

    /**
     * Store/Update role routes.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'route_names' => 'required|array|min:1',
        ]);

        $roleId = $request->role_id;
        $newRoutes = $request->route_names;

        // Get the current routes assigned to this role
        $existingRoutes = RoleRoute::where('role_id', $roleId)->pluck('route_name')->toArray();

        // Find differences
        $toAdd = array_diff($newRoutes, $existingRoutes);
        $toRemove = array_diff($existingRoutes, $newRoutes);

        // --- ADD new routes ---
        foreach ($toAdd as $routeName) {
            $route = RoleRoute::create([
                'role_id' => $roleId,
                'route_name' => $routeName,
            ]);

            // Log: route assigned
        }

        // --- REMOVE unselected routes ---
        if (!empty($toRemove)) {
            $removedRoutes = RoleRoute::where('role_id', $roleId)->whereIn('route_name', $toRemove)->get();

            foreach ($removedRoutes as $removed) {
                // Log before delete

                $removed->delete();
            }
        }

        return redirect()->route('role_routes.index')->with('success', 'Role routes updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoleRoute $roleRoute)
    {
        $roleRoute->delete();
        return redirect()->route('role_routes.index')->with('success', 'Role Route deleted successfully.');
    }
}
