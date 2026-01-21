<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // ✅ Use Local Scope 'accessible()' to filter users safely
            // This prevents the recursion error and ensures:
            // - Admins see ALL users
            // - Company Users see ONLY users in their company
            $query = User::accessible()->with(['role', 'company']);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('role', fn($user) => $user->role->name ?? 'N/A')
                ->addColumn('company', fn($user) => $user->company->company_name ?? 'N/A')
                ->addColumn('status', function ($user) {
                    return $user->status
                        ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>'
                        : '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Inactive</span>';
                })
                ->addColumn('action', function ($user) {
                    return '
                        <a href="'.route('users.edit', $user->id).'" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
                        <form action="'.route('users.destroy', $user->id).'" method="POST" style="display:inline" onsubmit="return confirm(\'Delete this user?\')">
                            '.csrf_field().method_field('DELETE').'
                            <button class="px-2 py-1 bg-red-600 text-white rounded">Delete</button>
                        </form>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ✅ Use Role::accessible() so non-admins cannot see/create "Admin" roles
        $roles = Role::accessible()->get();

        // ✅ Only Admins (Role 1) need to see the list of companies
        $companies = (Auth::user()->role_id == 1) ? Company::all() : [];

        return view('users.create', compact('roles', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateUser($request);
        $currentUser = Auth::user();

        // ✅ LOGIC: Company Assignment
        // If Admin: Use the selected company_id
        // If Non-Admin: Force use of their own company_id
        $companyId = ($currentUser->role_id == 1) 
            ? ($validated['company_id'] ?? null) 
            : $currentUser->company_id;

        User::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'role_id'          => $validated['role_id'],
            'whatsapp_api_key' => $validated['whatsapp_api_key'] ?? null,
            'company_id'       => $companyId,
            'status'           => $validated['status'] ?? 1,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // 🔒 SECURITY: Prevent editing users from other companies
        $this->authorizeAccess($user);

        // ✅ Fetch accessible roles only
        $roles = Role::accessible()->get();
        
        // ✅ Fetch companies only if Admin
        $companies = (Auth::user()->role_id == 1) ? Company::all() : [];

        return view('users.edit', compact('user', 'roles', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // 🔒 SECURITY
        $this->authorizeAccess($user);

        $validated = $this->validateUser($request, $user->id);
        $currentUser = Auth::user();

        $updateData = [
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'role_id'          => $validated['role_id'],
            'whatsapp_api_key' => $validated['whatsapp_api_key'] ?? null,
            'status'           => $validated['status']
        ];

        // ✅ Only Admin can change the company_id
        if ($currentUser->role_id == 1 && isset($validated['company_id'])) {
            $updateData['company_id'] = $validated['company_id'];
        }

        // Only hash password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // 🔒 SECURITY
        $this->authorizeAccess($user);

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Validate user data for create or update.
     */
    protected function validateUser(Request $request, int $userId = null): array
    {
        // ✅ Validation rule for company_id:
        // Required for Admins, Nullable/Ignored for others
        $companyRule = (Auth::user()->role_id == 1) ? 'nullable|exists:companies,id' : 'nullable';

        return $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $userId,
            'password'         => $userId ? 'nullable|min:6' : 'required|min:6',
            'role_id'          => 'required|exists:roles,id',
            'status'           => 'required|boolean',
            'whatsapp_api_key' => 'nullable|string',
            'company_id'       => $companyRule,
        ]);
    }

    /**
     * Helper: Abort 403 if user tries to access a record from another company
     */
    private function authorizeAccess(User $targetUser)
    {
        $currentUser = Auth::user();

        // If I am NOT Admin (Role 1) AND the target user has a different company ID -> BLOCK
        if ($currentUser->role_id != 1 && $targetUser->company_id !== $currentUser->company_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}