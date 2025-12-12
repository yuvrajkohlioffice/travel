<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
                ->addColumn('email', fn($row) => $row->email ?? '—')
                ->addColumn('phone', fn($row) => $row->phone ?? '—')
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
     * Store new company.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name'      => 'required|string|max:255',
            'owner_id'          => 'nullable|exists:users,id',
            'team_name'         => 'nullable|string|max:255',

            // NEW
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:20',

            'logo'              => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'scanner_image'     => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'scanner_upi_id'    => 'nullable|string|max:255',

            'bank_details'      => 'nullable|array',
            'bank_details.*'    => 'nullable|string',

            'whatsapp_api_key'  => 'nullable|string|max:255',
        ]);

        # Handle Logo Upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company/logo', 'public');
        }

        # Handle Scanner JSON
        $scanner = [];
        if ($request->hasFile('scanner_image')) {
            $scanner['image'] = $request->file('scanner_image')->store('company/scanner', 'public');
        }
        if ($request->scanner_upi_id) {
            $scanner['upi_id'] = $request->scanner_upi_id;
        }
        $validated['scanner_details'] = $scanner ?: null;

        # Bank Details JSON
        $validated['bank_details'] = $request->bank_details ?: null;

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
     * Update company.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name'      => 'required|string|max:255',
            'owner_id'          => 'nullable|exists:users,id',
            'team_name'         => 'nullable|string|max:255',

            // NEW
            'email'             => 'nullable|email|max:255',
            'phone'             => 'nullable|string|max:20',

            'logo'              => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'scanner_image'     => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'scanner_upi_id'    => 'nullable|string|max:255',

            'bank_details'      => 'nullable|array',
            'bank_details.*'    => 'nullable|string',

            'whatsapp_api_key'  => 'nullable|string|max:255',
        ]);

        # Logo Update
        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->getRawOriginal('logo'))) {
                Storage::disk('public')->delete($company->getRawOriginal('logo'));
            }
            $validated['logo'] = $request->file('logo')->store('company/logo', 'public');
        }

        # Scanner Update
        $scanner = $company->scanner_details ?? [];

        if ($request->hasFile('scanner_image')) {
            if (isset($scanner['image']) && Storage::disk('public')->exists($scanner['image'])) {
                Storage::disk('public')->delete($scanner['image']);
            }
            $scanner['image'] = $request->file('scanner_image')->store('company/scanner', 'public');
        }

        if ($request->scanner_upi_id) {
            $scanner['upi_id'] = $request->scanner_upi_id;
        }

        $validated['scanner_details'] = $scanner;

        # Bank JSON
        $validated['bank_details'] = $request->bank_details ?: null;

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
