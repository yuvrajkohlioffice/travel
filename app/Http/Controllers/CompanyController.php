<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies with eager loading.
     */


public function index(Request $request)
{
    if ($request->ajax()) {

        $query = Company::with('owner');

        return DataTables::of($query)
            ->addColumn('owner', fn($row) => $row->owner->name ?? '—')
            ->addColumn('team', fn($row) => $row->team_name ?? '—')
            ->addColumn('actions', function ($row) {
                return view('companies.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    return view('companies.index');
}



    /**
     * Show create form.
     */
    public function create()
    {
        $users = User::select('id', 'name')->get();
        return view('companies.create', compact('users'));
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_id'     => 'nullable|exists:users,id',
            'team_name'    => 'nullable|string|max:255',
        ]);

        Company::create($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Company Created Successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit(Company $company)
    {
        $users = User::select('id', 'name')->get();
        return view('companies.edit', compact('company', 'users'));
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'owner_id'     => 'nullable|exists:users,id',
            'team_name'    => 'nullable|string|max:255',
        ]);

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'Company Updated Successfully!');
    }

    /**
     * Soft delete company.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'Company Deleted Successfully!');
    }
}
