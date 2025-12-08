<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Package;
use App\Models\User;
use App\Models\LeadUser;
use Illuminate\Http\Request;
use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class LeadController extends Controller
{
     public function index(Request $request)
    {
        $user = auth()->user();

        $users = User::select('id', 'name')->get();
        $packages = Package::select('id', 'package_name')->orderBy('package_name')->get();

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

        // -----------------------------
        // BASE QUERY WITHOUT TIME FILTER
        // Used for both statusCounts and timeCounts
        // -----------------------------
        $baseNoTime = Lead::when($user->role_id != 1, fn($q) => $q->where(fn($q2) => $q2->where('user_id', $user->id)->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id))))
            ->when($filters['country'], fn($q, $v) => $q->where('country', $v))
            ->when($filters['district'], fn($q, $v) => $q->where('district', $v))
            ->when($filters['city'], fn($q, $v) => $q->where('city', $v))
            ->when($filters['lead_status'], fn($q, $v) => $q->where('lead_status', $v))
            ->when($filters['package_id'], fn($q, $v) => $q->where('package_id', $v))
            ->when($filters['user_id'], fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['assigned_to'], fn($q, $v) => $q->whereHas('assignedUsers', fn($uq) => $uq->where('user_id', $v)));

        // -----------------------------
        // STATUS COUNTS (NEVER CHANGE)
        // DO NOT APPLY TIME FILTER HERE
        // DO NOT APPLY STATUS FILTER HERE
        // -----------------------------
        $statusCounts = [
            'All' => (clone $baseNoTime)->count(),
            'Follow-up Taken' => (clone $baseNoTime)->where('status', 'Follow-up Taken')->count(),
            'Converted' => (clone $baseNoTime)->where('status', 'Converted')->count(),
            'Approved' => (clone $baseNoTime)->where('status', 'Approved')->count(),
            'Rejected' => (clone $baseNoTime)->where('status', 'Rejected')->count(),
        ];

        // -----------------------------
        // TIME COUNTS (APPLY STATUS FILTER ONLY)
        // -----------------------------
        $timeBase = (clone $baseNoTime)->when($filters['status'], fn($q, $v) => $q->where('status', $v));

        $timeCounts = [
            'all' => (clone $timeBase)->count(),
            'today' => (clone $timeBase)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'week' => (clone $timeBase)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => (clone $timeBase)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        // -----------------------------
        // MAIN QUERY FOR LIST (APPLY TIME + STATUS)
        // -----------------------------
        $leadsQuery = Lead::with(['package:id,package_name', 'lastFollowup.user:id,name', 'latestAssignedUser.user:id,name', 'createdBy:id,name'])
            ->mergeConstraintsFrom($baseNoTime) // apply same conditions
            ->when($filters['status'], fn($q, $v) => $q->where('status', $v))
            ->when($filters['time'] != 'all', function ($q) use ($filters) {
                if ($filters['time'] == 'today') {
                    $q->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                } elseif ($filters['time'] == 'week') {
                    $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($filters['time'] == 'month') {
                    $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                }
            });

        $leads = $leadsQuery
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

        return view('leads.index', compact('leads', 'packages', 'users', 'filters', 'statusCounts', 'timeCounts'));
    }

public function data(Request $request)
{
    $user = auth()->user();

    $query = Lead::with([
        'package:id,package_name',
        'createdBy:id,name',
        'latestAssignedUser.user:id,name'
    ])
    ->select('leads.*');

    // ---------- APPLY FILTERS ----------
    if ($request->country) {
        $query->where('country', $request->country);
    }
    if ($request->district) {
        $query->where('district', $request->district);
    }
    if ($request->city) {
        $query->where('city', $request->city);
    }
    if ($request->lead_status) {
        $query->where('lead_status', $request->lead_status);
    }
    if ($request->status) {
        $query->where('status', $request->status);
    }
    if ($request->package_id) {
        $query->where('package_id', $request->package_id);
    }
    if ($request->user_id) {
        $query->where('user_id', $request->user_id);
    }
    if ($request->assigned_to) {
        $query->whereHas('assignedUsers', function ($q) use ($request) {
            $q->where('user_id', $request->assigned_to);
        });
    }

    // Time filter
    if ($request->time == 'today') {
        $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
    } elseif ($request->time == 'week') {
        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    } elseif ($request->time == 'month') {
        $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    return DataTables::of($query)

        ->addIndexColumn()

        ->addColumn('client_info', function ($row) {
            return "
                <div class='font-semibold'>{$row->name}</div>
                <div class='text-xs text-gray-500'>{$row->email}</div>
                <div class='text-xs text-gray-500'>{$row->phone_code} {$row->phone_number}</div>
            ";
        })

        ->addColumn('location', function ($row) {
            return "{$row->city}, {$row->district}, {$row->country}";
        })

        ->addColumn('inquiry', function ($row) {
            return "<div class='text-sm'>{$row->inquiry_text}</div>";
        })

        ->addColumn('status_button', function ($row) {
            return "<span class='px-2 py-1 rounded bg-blue-100 text-blue-600 text-xs'>{$row->status}</span>";
        })

        ->addColumn('assigned', function ($row) {
            return optional($row->latestAssignedUser->user)->name ?? '—';
        })

        ->addColumn('actions', function ($row) {
            return "
                <button onclick='editLead({$row->id})' class='text-blue-600'>Edit</button>
            ";
        })

        ->rawColumns(['client_info', 'location', 'inquiry', 'status_button', 'actions'])

        ->make(true);
}
 
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
