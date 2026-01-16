<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use App\Models\Lead;
use App\Models\Package;
use App\Models\User;
use App\Models\LeadStatus;
use App\Models\LeadUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class LeadController extends Controller
{
    // =========================================================================
    // PRIVATE HELPER METHODS (DRY Logic)
    // =========================================================================

    /**
     * 1. Centralized Base Query with Role-Based Access Control (RBAC).
     * This ensures Admin, Owner, and Employees only see what they are allowed to see.
     */
    private function getBaseQuery()
    {
        $user = auth()->user();
        $companyId = $user->company_id;
        $query = Lead::query();

        // Admin (Role 1): Can see everything (including soft deleted)
        if ($user->role_id == 1) {
            return $query->withTrashed();
        }

        // Logic for Company Owner vs Employee
        $isOwner = $companyId && $user->id === optional($user->company)->owner_id;

        return $query->where(function ($q) use ($user, $isOwner, $companyId) {
            if ($isOwner) {
                // Owner: See all leads in the company
                $q->whereHas('createdBy', fn($q2) => $q2->where('company_id', $companyId));
            } else {
                // Employee: See only own leads or assigned leads
                $q->where('user_id', $user->id)
                  ->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id));
            }
        });
    }

    /**
     * 2. Standard Filters (Search, Location, Dropdowns).
     * Does NOT handle date filters (handled separately due to complexity).
     */
    private function applyFilters($query, Request $request)
    {
        // ID Search
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Text Search (Name, Email, Phone)
        if ($request->filled('client_name')) {
            $term = $request->client_name;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone_number', 'like', "%{$term}%");
            });
        }

        // Location Search
        if ($request->filled('location')) {
            $loc = $request->location;
            $query->where(function ($q) use ($loc) {
                $q->where('country', 'like', "%{$loc}%")
                  ->orWhere('district', 'like', "%{$loc}%")
                  ->orWhere('city', 'like', "%{$loc}%");
            });
        }

        // Status Filter (Pending, Approved, etc.)
        // Note: 'Follow-up Taken' logic is handled in the Date Filter section usually, 
        // but we filter the main status column here.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lead Category (Hot, Warm, Cold)
        if ($request->filled('lead_status')) {
            $query->where('lead_status', $request->lead_status);
        }

        // Assigned User Filter
        if ($request->filled('assigned')) {
            $query->whereHas('latestAssignedUser.user', fn($q) => $q->where('name', $request->assigned));
        }

        // Additional Index Page Filters
        foreach (['country', 'district', 'city', 'package_id', 'user_id'] as $key) {
            if ($request->filled($key)) {
                $query->where($key, $request->input($key));
            }
        }

        return $query;
    }

    // =========================================================================
    // MAIN CONTROLLER METHODS
    // =========================================================================

    /**
     * Display the main Leads Dashboard (Index).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // 1. Fetch Statuses (Global + Company Specific)
        // Using the logic from your LeadStatus model scope essentially
        $leadStatuses = LeadStatus::where('is_active', true)
            ->where(fn($q) => $q->where('company_id', $companyId)->orWhereNull('company_id'))
            ->orderByRaw('company_id IS NULL') // Prefer company specific
            ->orderBy('order_by', 'asc')
            ->get()
            ->unique('name')
            ->values();

        // 2. Fetch Dependencies
        $users = User::select('id', 'name')->get();
        $packages = Package::select('id', 'package_name')->orderBy('package_name')->get();
        
        // 3. Prepare Filters for View
        $filters = $request->only(['country', 'district', 'city', 'lead_status', 'status', 'package_id', 'user_id', 'assigned_to', 'time']);
        $filters['time'] = $filters['time'] ?? 'all';

        // 4. Initial Load (Server Side Rendered Table)
        // Note: The AJAX 'getLeadsData' will handle the heavy lifting later.
        $leads = $this->getBaseQuery()
            ->with(['package:id,package_name', 'latestAssignedUser.user:id,name', 'createdBy:id,name'])
            ->orderByDesc('created_at')
            ->paginate(50);

        // 5. Initial Counts (Placeholder or Basic)
        // We will load the precise counts via AJAX to speed up page load
        $timeCounts = ['all' => 0, 'today' => 0, 'week' => 0, 'month' => 0]; 
        $statusOthersCounts = []; 

        return view('leads.index', compact('leads', 'packages', 'users', 'filters', 'statusOthersCounts', 'timeCounts', 'leadStatuses'));
    }

    /**
     * AJAX: Get Data for DataTables.
     */
    public function getLeadsData(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // Fetch statuses for the dropdown inside the table
        $leadStatuses = LeadStatus::where('is_active', true)
            ->where(fn($q) => $q->where('company_id', $companyId)->orWhereNull('company_id'))
            ->orderBy('order_by', 'asc')
            ->get()->unique('name');

        // 1. Start with Base Query
        $query = $this->getBaseQuery();
        
        // 2. Eager Load Relationships (Optimized)
        $query->with([
            'package:id,package_name', 
            'latestAssignedUser.user:id,name', 
            'latestAssignedUser.assignedBy:id,name', 
            'createdBy:id,name', 
            'lastFollowup.user:id,name' // Assuming 'lastFollowup' relation exists on Lead model
        ]);

        // 3. Apply Standard Filters
        $query = $this->applyFilters($query, $request);

        // 4. Apply Date Logic (The Critical Part)
        // If status is 'Follow-up Taken', we filter by the next_followup_date.
        // Otherwise, we filter by created_at.
        
        if ($request->status === 'Follow-up Taken') {
            // Filter using the Related Followup Model
            $query->whereHas('lastFollowup', function ($q) use ($request) {
                match ($request->date_range) {
                    'today'     => $q->whereDate('next_followup_date', today()),
                    'yesterday' => $q->whereDate('next_followup_date', today()->subDay()),
                    'week'      => $q->whereBetween('next_followup_date', [now()->startOfWeek(), now()->endOfWeek()]),
                    'month'     => $q->whereMonth('next_followup_date', now()->month)->whereYear('next_followup_date', now()->year),
                    default     => null,
                };
            });
            // If viewing follow-ups, usually better to sort by the followup date
            // But we can stick to created_at or sort by followup date if needed:
            // $query->join(...)->orderBy('next_followup_date');
        } elseif ($request->filled('date_range')) {
            // Standard Date Filter (Created At)
            match ($request->date_range) {
                'today'     => $query->whereDate('created_at', today()),
                'yesterday' => $query->whereDate('created_at', today()->subDay()),
                'week'      => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                'month'     => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                default     => null,
            };
        }

        $query->orderByDesc('created_at');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', fn($lead) => '<input type="checkbox" class="row-checkbox h-4 w-4" data-id="' . $lead->id . '">')
            ->addColumn('client_info', fn($lead) => view('leads.partials.client_info', compact('lead'))->render())
            ->addColumn('location', fn($lead) => "{$lead->country}<br>{$lead->district}<br>{$lead->city}")
            ->addColumn('reminder', fn($lead) => view('leads.partials.reminder', compact('lead'))->render())
            ->addColumn('inquiry', fn($lead) => $lead->package->package_name ?? Str::limit($lead->inquiry_text, 20))
            ->addColumn('proposal', fn($lead) => view('leads.partials.proposal', compact('lead'))->render())
            ->addColumn('status', fn($lead) => view('leads.partials.status_dropdown', ['lead' => $lead, 'leadStatuses' => $leadStatuses])->render())
            ->addColumn('assigned', fn($lead) => view('leads.partials.assigned', compact('lead'))->render())
            ->addColumn('action', fn($lead) => view('leads.partials.actions', compact('lead'))->render())
            ->rawColumns(['checkbox', 'client_info', 'location', 'reminder', 'inquiry', 'proposal', 'status', 'assigned', 'action'])
            ->make(true);
    }

    /**
     * AJAX: Get Counts (Optimized Single Query).
     */
    public function getLeadsCounts(Request $request)
    {
        $baseQuery = $this->getBaseQuery();
        $baseQuery = $this->applyFilters($baseQuery, $request);

        // Determine which date column to count against
        $isFollowUpMode = ($request->status === 'Follow-up Taken');

        if ($isFollowUpMode) {
            // Join is necessary for SQL aggregation on a related column
            // Assuming table name is 'followups' or 'lead_followups'. Based on convention, checking 'lead_followups'.
            // Also need to ensure we only join the LATEST followup if multiple exist.
            $baseQuery->whereHas('lastFollowup'); 
            
            // For accurate counting on the 'latest' date, we usually use a subquery join or window function.
            // Simplified approach: Join the table.
            $baseQuery->join('followups', 'leads.id', '=', 'followups.lead_id'); 
            // Add condition to only look at latest if needed, but for filtering 'next_followup_date', usually just valid ones match.
            
            $dateCol = 'followups.next_followup_date';
        } else {
            $dateCol = 'leads.created_at';
        }

        // Single DB Query for all time buckets
        $counts = $baseQuery->selectRaw("
            COUNT(*) as all_count,
            SUM(CASE WHEN DATE($dateCol) = CURDATE() THEN 1 ELSE 0 END) as today_count,
            SUM(CASE WHEN DATE($dateCol) = SUBDATE(CURDATE(), 1) THEN 1 ELSE 0 END) as yesterday_count,
            SUM(CASE WHEN YEARWEEK($dateCol, 1) = YEARWEEK(CURDATE(), 1) THEN 1 ELSE 0 END) as week_count,
            SUM(CASE WHEN MONTH($dateCol) = MONTH(CURDATE()) AND YEAR($dateCol) = YEAR(CURDATE()) THEN 1 ELSE 0 END) as month_count
        ")->first();

        return response()->json([
            'all'       => $counts->all_count ?? 0,
            'today'     => $counts->today_count ?? 0,
            'yesterday' => $counts->yesterday_count ?? 0,
            'week'      => $counts->week_count ?? 0,
            'month'     => $counts->month_count ?? 0,
        ]);
    }

    // =========================================================================
    // CRUD OPERATIONS
    // =========================================================================

    public function create()
    {
        $packages = Package::select('id', 'package_name')->orderBy('package_name')->get();
        return view('leads.create', compact('packages'));
    }

    public function store(Request $request)
    {
        // 1. Sanitize
        if ($request->filled('phone_number')) {
            $request->merge(['phone_number' => preg_replace('/[^0-9]/', '', $request->phone_number)]);
        }
        if ($request->filled('phone_code')) {
            $request->merge(['phone_code' => preg_replace('/[^0-9]/', '', $request->phone_code)]);
        }

        // 2. Validate
        $validated = $request->validate([
            'name'         => 'required|max:255',
            'email'        => 'nullable|email',
            'phone_number' => 'nullable|numeric|digits_between:7,15',
            'phone_code'   => 'nullable|numeric',
        ]);

        // 3. Prepare & Save
        $leadData = array_merge(
            $validated,
            $request->only(['company_name', 'people_count', 'district', 'country', 'city', 'client_category', 'lead_status', 'lead_source', 'website', 'package_id', 'inquiry_text']),
            ['user_id' => auth()->id()]
        );

        $lead = Lead::create($leadData);

        // 4. Assign to Creator
        LeadUser::create([
            'lead_id'     => $lead->id,
            'user_id'     => auth()->id(),
            'assigned_by' => auth()->id(),
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        return view('leads.show', compact('lead'));
    }

    public function showJson(Lead $lead)
    {
        return response()->json($lead->only([
            'id', 'name', 'company_name', 'email', 'country', 'district', 
            'phone_code', 'phone_number', 'city', 'client_category', 
            'lead_status', 'lead_source', 'website', 'status', 
            'package_id', 'inquiry_text', 'people_count', 'child_count'
        ]));
    }

    public function edit(Lead $lead)
    {
        $packages = Package::select('id', 'package_name')->get();
        return view('leads.edit', compact('lead', 'packages'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'name'         => 'required|max:255',
            'email'        => 'nullable|email',
            'phone_number' => 'nullable|max:15',
        ]);

        $lead->update(array_merge(
            $validated,
            $request->only(['company_name', 'people_count', 'child_count', 'district', 'country', 'phone_code', 'city', 'client_category', 'lead_status', 'lead_source', 'website', 'package_id', 'inquiry_text', 'status'])
        ));

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully',
            'lead'    => $lead,
        ]);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate(['status' => 'required|string|max:255']);
        $lead->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated successfully',
            'status'  => $lead->status,
        ]);
    }

    public function destroy(Lead $lead)
    {
        if ($lead->assignedUsers()->exists()) {
            $lead->assignedUsers()->delete();
        }
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    // =========================================================================
    // ASSIGNMENT & BULK ACTIONS
    // =========================================================================

    public function assignForm(Lead $lead)
    {
        $assignedUsers = $lead->assignedUsers()->with('user:id,name', 'assignedBy:id,name')->get();
        $assignedUserIds = $assignedUsers->pluck('user_id');
        $currentUserId = auth()->id();

        $users = User::select('id', 'name', 'email')
            ->whereNotIn('id', $assignedUserIds)
            ->where('id', '!=', $currentUserId)
            ->where('role_id', '!=', 1)
            ->orderBy('name')
            ->get();

        return view('leads.assign', compact('lead', 'users', 'assignedUsers'));
    }

    public function assignStore(Request $request, Lead $lead)
    {
        $request->validate(['user_ids' => 'required|array|min:1']);

        $existing = $lead->assignedUsers()->whereIn('user_id', $request->user_ids)->pluck('user_id')->toArray();
        $newAssignments = array_diff($request->user_ids, $existing);

        foreach ($newAssignments as $userId) {
            LeadUser::create([
                'lead_id'     => $lead->id,
                'user_id'     => $userId,
                'assigned_by' => auth()->id(),
            ]);
        }
        return redirect()->route('leads.index')->with('success', 'Lead assigned successfully.');
    }

    public function deleteAssignment($id)
    {
        LeadUser::destroy($id);
        return redirect()->back()->with('success', 'Assignment removed successfully!');
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'lead_ids'   => 'required|array|min:1',
            'lead_ids.*' => 'integer|exists:leads,id',
            'user_id'    => 'required|exists:users,id',
        ]);

        $leadIds = $request->lead_ids;
        $userId  = $request->user_id;
        $assignedBy = auth()->id();

        $alreadyAssigned = LeadUser::whereIn('lead_id', $leadIds)
            ->where('user_id', $userId)
            ->pluck('lead_id')
            ->toArray();

        $toAssign = array_diff($leadIds, $alreadyAssigned);

        if (count($toAssign) > 0) {
            $insertData = [];
            $now = now();
            foreach ($toAssign as $leadId) {
                $insertData[] = [
                    'lead_id'     => $leadId,
                    'user_id'     => $userId,
                    'assigned_by' => $assignedBy,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
            LeadUser::insert($insertData);
        }

        return response()->json([
            'success'        => true,
            'assigned_count' => count($toAssign),
            'skipped'        => count($alreadyAssigned),
            'message'        => 'Selected leads assigned successfully!',
        ]);
    }

    public function importLeads(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        Excel::import(new LeadsImport(auth()->id()), $request->file('file'));
        return back()->with('success', 'Leads imported successfully!');
    }
}