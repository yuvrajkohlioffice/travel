<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Package;
use App\Models\User;
use App\Models\LeadUser;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with(['package'])
            ->orderByRaw(
                "
                CASE
                    WHEN lead_status = 'Hot' THEN 1
                    WHEN lead_status = 'Warm' THEN 2
                    WHEN lead_status = 'Cold' THEN 3
                    ELSE 4
                END
            ",
            )
            ->latest()
            ->paginate(20); // Paging improves performance

        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        $packages = Package::select('id', 'package_name')->orderBy('package_name')->get();
        return view('leads.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|max:15',
        ]);

        Lead::create($validated + $request->only(['company_name', 'district', 'country', 'phone_code', 'city', 'client_category', 'lead_status', 'lead_source', 'website', 'package_id', 'inquiry_text']));

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $packages = Package::select('id', 'package_name')->get();
        return view('leads.edit', compact('lead', 'packages'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|max:15',
        ]);

        $lead->update($validated + $request->only(['company_name', 'district', 'country', 'phone_code', 'city', 'client_category', 'lead_status', 'lead_source', 'website', 'package_id', 'inquiry_text']));

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->assignedUsers()->delete(); // delete related assignments
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function assignForm(Lead $lead)
    {
        $assignedUsers = $lead->assignedUsers()->with('user:id,name', 'assignedBy:id,name')->get();

        $assignedUserIds = $assignedUsers->pluck('user_id');

        $users = User::select('id', 'name', 'email')->whereNotIn('id', $assignedUserIds)->where('role_id', '!=', 1)->orderBy('name')->get();

        return view('leads.assign', compact('lead', 'users', 'assignedUsers'));
    }

    public function deleteAssignment($id)
    {
        LeadUser::destroy($id);
        return redirect()->back()->with('success', 'Assignment removed successfully!');
    }

    public function assignStore(Request $request, Lead $lead)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
        ]);

        $existing = $lead->assignedUsers()->whereIn('user_id', $request->user_ids)->pluck('user_id')->toArray();

        $newAssignments = array_diff($request->user_ids, $existing);

        foreach ($newAssignments as $userId) {
            LeadUser::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'assigned_by' => auth()->id(),
            ]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead assigned successfully.');
    }
}
