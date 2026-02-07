<?php

namespace App\Http\Controllers;

use App\Models\PackageCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class PackageCategoryController extends Controller
{
    /**
     * Display the list of categories with Role-based filtering.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $query = PackageCategory::with('company')->select('package_category.*');

            // Logic: Role 1 (Admin) sees all. Others see their Company + Global.
            if ($user->role_id != 1) {
                $query->where(function ($q) use ($user) {
                    $q->where('company_id', $user->company_id)
                      ->orWhere('is_global', 1);
                });
            }

            return DataTables::of($query)
                ->addColumn('company_name', function ($row) {
                    if ($row->is_global) {
                        return '<span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-bold">GLOBAL</span>';
                    }
                    return $row->company->name ?? '<span class="text-gray-400">N/A</span>';
                })
                ->addColumn('action', function ($row) use ($user) {
                    $actions = '<div class="flex gap-3">';

                    // Permission Check: Can they Edit?
                    // Admins can edit anything. Users can only edit their own company categories (not global).
                    if ($user->role_id == 1 || (!$row->is_global && $row->company_id == $user->company_id)) {
                        $actions .= '<a href="'.route('package-categories.edit', $row->id).'" class="text-blue-600 hover:text-blue-900 shadow-sm">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                     </a>';
                    }

                    // Permission Check: Can they Delete?
                    if ($user->role_id == 1 || (!$row->is_global && $row->company_id == $user->company_id)) {
                        $actions .= '
                            <form action="'.route('package-categories.destroy', $row->id).'" method="POST" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this?\')">
                                '.csrf_field().method_field('DELETE').'
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['action', 'company_name'])
                ->make(true);
        }

        return view('package_categories.index');
    }

    public function create()
    {
        return view('package_categories.create');
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        
        // Only Admin (Role 1) can set is_global to true
        $isGlobal = ($request->has('is_global') && $user->role_id == 1) ? 1 : 0;

        PackageCategory::create([
            'name'       => $request->name,
            'is_global'  => $isGlobal,
            'company_id' => $user->company_id, // Creator's company ID
        ]);

        return redirect()->route('package-categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show Edit form with Security Check.
     */
    public function edit(PackageCategory $packageCategory)
    {
        $user = Auth::user();

        // Security: Prevent non-admins from editing Global or other companies' categories
        if ($user->role_id != 1) {
            if ($packageCategory->is_global || $packageCategory->company_id != $user->company_id) {
                return redirect()->route('package-categories.index')->with('error', 'You do not have permission to edit this category.');
            }
        }

        return view('package_categories.edit', compact('packageCategory'));
    }

    /**
     * Update category with Global-toggle protection.
     */
    public function update(Request $request, PackageCategory $packageCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $user = Auth::user();

        // 1. Security Check
        if ($user->role_id != 1 && ($packageCategory->is_global || $packageCategory->company_id != $user->company_id)) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Prepare Data
        $data = ['name' => $request->name];

        // 3. Only Admin can change the Global status
        if ($user->role_id == 1) {
            $data['is_global'] = $request->has('is_global') ? 1 : 0;
        }

        $packageCategory->update($data);

        return redirect()->route('package-categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category with strict Role & Global checks.
     */
    public function destroy(PackageCategory $packageCategory)
    {
        $user = Auth::user();

        // Rule 1: Admin can delete everything.
        if ($user->role_id == 1) {
            $packageCategory->delete();
            return redirect()->route('package-categories.index')->with('success', 'Category deleted by Admin.');
        }

        // Rule 2: Non-admins cannot delete Global items.
        if ($packageCategory->is_global) {
            return redirect()->route('package-categories.index')->with('error', 'Global categories can only be deleted by a Super Admin.');
        }

        // Rule 3: Users can only delete if it belongs to their company.
        if ($packageCategory->company_id == $user->company_id) {
            $packageCategory->delete();
            return redirect()->route('package-categories.index')->with('success', 'Category deleted successfully.');
        }

        return redirect()->route('package-categories.index')->with('error', 'Permission denied.');
    }
}