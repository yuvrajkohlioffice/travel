<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Package;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LeadUser;
class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with('package')
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
            ->latest() // optional: newest inside each group
            ->get();

        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        $packages = Package::all();
        return view('leads.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Lead::create($request->all());

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $packages = Package::all();
        return view('leads.edit', compact('lead', 'packages'));
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $lead->update($request->all());

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }
    public function assignForm(Lead $lead)
    {
        // Users already assigned
        $assignedUsers = $lead->assignedUsers()->with('user', 'assignedBy')->get();

        // Get IDs of already assigned users
        $assignedUserIds = $assignedUsers->pluck('user_id');

        // Get users who are NOT already assigned AND whose role_id is NOT 1
        $users = User::whereNotIn('id', $assignedUserIds)->where('role_id', '!=', 1)->get();

        return view('leads.assign', compact('lead', 'users', 'assignedUsers'));
    }

    public function deleteAssignment($id)
    {
        $assignment = LeadUser::findOrFail($id);
        $assignment->delete();

        return redirect()->back()->with('success', 'Assignment removed successfully!');
    }

    public function assignStore(Request $request, Lead $lead)
    {
        $request->validate([
            'user_ids' => 'required|array',
        ]);

        foreach ($request->user_ids as $userId) {
            LeadUser::create([
                'lead_id' => $lead->id,
                'user_id' => $userId,
                'assigned_by' => auth()->id(),
            ]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead assigned successfully.');
    }
}
