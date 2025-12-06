<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Package;
use App\Models\User;
use App\Models\LeadUser;
use Illuminate\Http\Request;
use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    public function showJson(Lead $lead)
    {
        // Include only the fields needed for the modal
        return response()->json([
            'id' => $lead->id,
            'name' => $lead->name,
            'company_name' => $lead->company_name,
            'email' => $lead->email,
            'country' => $lead->country,
            'district' => $lead->district,
            'phone_code' => $lead->phone_code,
            'phone_number' => $lead->phone_number,
            'city' => $lead->city,
            'client_category' => $lead->client_category,
            'lead_status' => $lead->lead_status,
            'lead_source' => $lead->lead_source,
            'website' => $lead->website,
            'status' => $lead->status,
            'package_id' => $lead->package_id,
            'inquiry_text' => $lead->inquiry_text,
            'people_count' => $lead->people_count,
            'child_count' => $lead->child_count,
        ]);
    }
    public function index(Request $request)
    {
        $user = auth()->user();

        // Users & Packages for filters
        $users = User::select('id', 'name')->get();
        $packages = Package::select('id', 'package_name')->orderBy('package_name')->get();

        // Filters from request
        $filters = [
            'country' => $request->country,
            'district' => $request->district,
            'city' => $request->city,
            'lead_status' => $request->lead_status,
            'status' => $request->status,
            'package_id' => $request->package_id,
            'user_id' => $request->user_id,
            'assigned_to' => $request->assigned_to,
            'time' => $request->time ?? 'all',
        ];

        $baseQuery = Lead::with(['package:id,package_name', 'lastFollowup.user:id,name', 'latestAssignedUser.user:id,name', 'createdBy:id,name'])
            ->when($user->role_id != 1, fn($q) => $q->where(fn($q2) => $q2->where('user_id', $user->id)->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id))))
            ->when($filters['country'], fn($q, $v) => $q->where('country', $v))
            ->when($filters['district'], fn($q, $v) => $q->where('district', $v))
            ->when($filters['city'], fn($q, $v) => $q->where('city', $v))
            ->when($filters['lead_status'], fn($q, $v) => $q->where('lead_status', $v))
            ->when($filters['package_id'], fn($q, $v) => $q->where('package_id', $v))
            ->when($filters['user_id'], fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['assigned_to'], fn($q, $v) => $q->whereHas('assignedUsers', fn($uq) => $uq->where('user_id', $v)))
            ->when($filters['time'] && $filters['time'] != 'all', function ($q) use ($filters) {
                if ($filters['time'] === 'today') {
                    $q->whereBetween('updated_at', [now()->startOfDay(), now()->endOfDay()]);
                } elseif ($filters['time'] === 'week') {
                    $q->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($filters['time'] === 'month') {
                    $q->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()]);
                }
            });
        $leads = (clone $baseQuery)
            ->when($filters['status'], fn($q, $v) => $q->where('status', $v))
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
            ->latest('id')
            ->get();

        // Get filtered leads
        // Get counts for buttons (status + time)
        $statusCounts = [
            'All' => (clone $baseQuery)->count(),
            'Follow-up Taken' => (clone $baseQuery)->where('status', 'Follow-up Taken')->count(),
            'Converted' => (clone $baseQuery)->where('status', 'Converted')->count(),
            'Rejected' => (clone $baseQuery)->where('status', 'Rejected')->count(),
            'Approved' => (clone $baseQuery)->where('status', 'Approved')->count(),
        ];

        return view('leads.index', compact('leads', 'packages', 'users', 'filters', 'statusCounts'));
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
        $lead = Lead::create($validated + $request->only(['company_name', 'people_count', 'district', 'country', 'phone_code', 'city', 'client_category', 'lead_status', 'lead_source', 'website', 'package_id', 'inquiry_text']) + ['user_id' => auth()->id()]);

        LeadUser::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'assigned_by' => auth()->id(),
        ]);

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

        $lead->update($validated + $request->only(['company_name', 'people_count', 'child_count', 'district', 'country', 'phone_code', 'city', 'client_category', 'lead_status', 'lead_source', 'website', 'package_id', 'inquiry_text', 'status']));

        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'lead' => $lead, // optional, in case you want to update frontend dynamically
        ]);
    }
    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $lead->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully',
            'status' => $lead->status,
        ]);
    }

    public function destroy(Lead $lead)
    {
        $lead->assignedUsers()->delete();
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function assignForm(Lead $lead)
    {
        $assignedUsers = $lead->assignedUsers()->with('user:id,name', 'assignedBy:id,name')->get();

        $assignedUserIds = $assignedUsers->pluck('user_id');

        $currentUserId = auth()->id();

        $users = User::select('id', 'name', 'email')->whereNotIn('id', $assignedUserIds)->where('id', '!=', $currentUserId)->where('role_id', '!=', 1)->orderBy('name')->get();

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

    public function importLeads(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        $userId = auth()->id();

        Excel::import(new LeadsImport($userId), $request->file('file'));

        return back()->with('success', 'Leads imported successfully!');
    }
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array|min:1',
            'user_id' => 'required|exists:users,id',
        ]);

        $leadIds = $request->lead_ids;
        $userId = $request->user_id;
        $assignedBy = auth()->id();

        foreach ($leadIds as $leadId) {
            // Check if already assigned
            $exists = LeadUser::where('lead_id', $leadId)->where('user_id', $userId)->exists();
            if (!$exists) {
                LeadUser::create([
                    'lead_id' => $leadId,
                    'user_id' => $userId,
                    'assigned_by' => $assignedBy,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Selected leads assigned successfully!',
        ]);
    }
}
