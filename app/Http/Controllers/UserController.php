<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Yajra\DataTables\DataTables;



class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    if ($request->ajax()) {
        $users = User::with(['role', 'company']);

        return DataTables::of($users)
            ->addIndexColumn()

            ->addColumn('role', function ($user) {
                return $user->role->name ?? 'N/A';
            })

            ->addColumn('company', function ($user) {
                return $user->company->company_name ?? 'N/A';
            })

            ->addColumn('status', function ($user) {
                return $user->status
                    ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>'
                    : '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Inactive</span>';
            })

            ->addColumn('action', function ($user) {
                $buttons = '
                    <a href="'.route('users.edit', $user->id).'"
                        class="px-2 py-1 bg-yellow-500 text-white rounded">
                        Edit
                    </a>

                    <form action="'.route('users.destroy', $user->id).'" method="POST"
                        style="display:inline"
                        onsubmit="return confirm(\'Delete this user?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button class="px-2 py-1 bg-red-600 text-white rounded">
                            Delete
                        </button>
                    </form>
                ';

            
                return $buttons;
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
        $roles = Role::all();
        $companies = Company::all();
        return view('users.create', compact('roles', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateUser($request);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'whatsapp_api_key' =>$validated['whatsapp_api_key'],
            'company_id' => $validated['company_id'] ?? null,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $companies = Company::all();
        return view('users.edit', compact('user', 'roles', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user->id);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'company_id' => $validated['company_id'] ?? null,
            'whatsapp_api_key' =>$validated['whatsapp_api_key'],
            'status' =>$validated['status']
        ];

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
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    /**
     * Validate user data for create or update.
     */
    protected function validateUser(Request $request, int $userId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required',
            'whatsapp_api_key'=> 'nullable',
            'company_id' => 'nullable|exists:companies,id',
        ]);
    }
}
