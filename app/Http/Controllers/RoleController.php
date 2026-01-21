<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ✅ FIX: accessible() is a scope, so we must chain ->get() to execute the query
        $roles = Role::accessible()->get();
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // ✅ Unique validation ensures no duplicate role names
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name
        ]);

        return redirect()->route('roles.index')
                         ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // 🔒 SECURITY: Prevent unauthorized access to Admin role
        $this->authorizeRoleAccess($role);

        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // 🔒 SECURITY
        $this->authorizeRoleAccess($role);

        $request->validate([
            // Ignore current role ID during unique check
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name
        ]);

        return redirect()->route('roles.index')
                         ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // 🔒 SECURITY
        $this->authorizeRoleAccess($role);

        // 1. Prevent deleting the Super Admin Role (ID 1)
        if ($role->id === 1) {
            return back()->with('error', 'The Super Admin role cannot be deleted.');
        }

        // 2. Prevent deleting roles that have users assigned
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete this role because users are currently assigned to it.');
        }

        $role->delete();

        return redirect()->route('roles.index')
                         ->with('success', 'Role deleted successfully.');
    }

    /**
     * 🔒 Helper: Protect Critical Roles
     * Ensures non-admins cannot touch Role ID 1 via direct URL access.
     */
    private function authorizeRoleAccess(Role $targetRole)
    {
        // If trying to access Admin Role (ID 1) AND user is NOT Admin (ID 1)
        if ($targetRole->id === 1 && Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized action. You cannot modify the Super Admin role.');
        }
    }
}