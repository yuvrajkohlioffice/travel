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

    // 1. Fetch Defined Statuses (Name, Color, Icon)
    // We keyBy('name') to make looking up colors easier later
    $leadStatuses = LeadStatus::where('is_active', true)
        ->where(fn($q) => $q->where('company_id', $companyId)->orWhereNull('company_id'))
        ->orderBy('order_by', 'asc')
        ->get();

    $users = User::select('id', 'name')->get();
    $packages = Package::select('id', 'package_name')->orderBy('package_name')->get();

    // 2. Prepare Filters
    $filters = $request->only(['country', 'district', 'city', 'lead_status', 'status', 'package_id', 'user_id', 'assigned_to', 'time']);
    $filters['time'] = $filters['time'] ?? 'all';

    // 3. Get Base Query (Permissions only)
    $baseQuery = $this->getBaseQuery();
    
    // Apply filters to the base query so the top cards reflect the search results
    // (Optional: If you want cards to always show total database counts, remove the next line)
    $filteredQuery = $this->applyFilters(clone $baseQuery, $request);

    // 4. Time Counts (For the date tabs)
    // We use the filtered query for this to respect other filters like 'City' or 'User'
    // Note: This logic assumes 'created_at' for standard leads. 
    $timeCounts = [
        'all'   => (clone $filteredQuery)->count(),
        'today' => (clone $filteredQuery)->whereDate('created_at', today())->count(),
        'week'  => (clone $filteredQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        'month' => (clone $filteredQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
    ];

    // 5. Status Counts (The Array you asked for)
    // OPTIMIZATION: Group by status in SQL to get all counts in 1 query
    $statusCountsRaw = (clone $filteredQuery)
        ->select('status', DB::raw('COUNT(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();

    $statusOthersCounts = [];

    // A. Add "All" Card
    $statusOthersCounts['All'] = [
        'count' => $timeCounts['all'],
        'color' => 'bg-gray-500 text-white', // Default Gray
        'icon'  => 'fa-layer-group', // Default Icon
    ];

    // B. Add Dynamic Status Cards
    foreach ($leadStatuses as $status) {
        $count = $statusCountsRaw[$status->name] ?? 0; // Get count or 0 if none

        // Determine Icon based on status name (Since icon isn't in DB, we map it here)
        $icon = match($status->name) {
            'Pending'         => 'fa-hourglass-start',
            'Approved'        => 'fa-check-double',
            'Quotation Sent'  => 'fa-file-invoice-dollar',
            'Follow-up Taken' => 'fa-phone-volume',
            'Converted'       => 'fa-thumbs-up',
            'Lost'            => 'fa-thumbs-down',
            'On Hold'         => 'fa-pause-circle',
            'Rejected'        => 'fa-ban',
            default           => 'fa-circle-info'
        };

        $statusOthersCounts[$status->name] = [
            'count' => $count,
            'color' => $status->color ?? 'bg-blue-500 text-white', // Use DB color
            'icon'  => $icon,
        ];
    }

    // 6. Fetch Table Data (Pagination)
    $leads = $filteredQuery
        ->with(['package:id,package_name', 'latestAssignedUser.user:id,name', 'createdBy:id,name'])
        ->orderByDesc('created_at')
        ->paginate(50)
        ->withQueryString();

    return view('leads.index', compact('leads', 'packages', 'users', 'filters', 'statusOthersCounts', 'timeCounts', 'leadStatuses'));
}

    /**
     * AJAX: Get Data for DataTables.
     */
   public function getLeadsData(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        // Fetch statuses for dropdown
        $leadStatuses = LeadStatus::where('is_active', true)
            ->where(fn($q) => $q->where('company_id', $companyId)->orWhereNull('company_id'))
            ->orderBy('order_by', 'asc')
            ->get()->unique('name');

        // 1. Base Query & Relationships
        $query = $this->getBaseQuery();
        
        $query->with([
            'package:id,package_name', 
            'latestAssignedUser.user:id,name', 
            'latestAssignedUser.assignedBy:id,name', 
            'createdBy:id,name', 
            'lastFollowup.user:id,name' 
        ]);

        // 2. Apply Standard Filters (Search, Location, etc.)
        $query = $this->applyFilters($query, $request);

        // 3. Conditional Logic: Follow-up vs Standard
        if ($request->status === 'Follow-up Taken') {
            
            // JOIN: We join followups to allow Sorting & Filtering by 'next_followup_date'
            $query->join('followups', 'leads.id', '=', 'followups.lead_id');
            
            // SELECT: Avoid column collisions (id, created_at) by selecting only leads.*
            $query->select('leads.*');

            // DATE FILTER: Apply to 'followups.next_followup_date'
            if ($request->filled('date_range')) {
                $this->applyDateScope($query, $request->date_range, 'followups.next_followup_date');
            }

            // SORTING: Show upcoming/overdue tasks FIRST (Ascending)
            $query->orderBy('followups.next_followup_date', 'asc');

            // DISTINCT: Ensure lead only appears once even if date range matches multiple follow-ups
            $query->distinct();

        } else {
            
            // STANDARD MODE: Filter by Lead Creation Date
            if ($request->filled('date_range')) {
                $this->applyDateScope($query, $request->date_range, 'leads.created_at');
            }

            // SORTING: Show newest leads FIRST (Descending)
            $query->orderByDesc('leads.created_at');
        }

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
     * Helper to keep code DRY: Applies date filters to a specific column.
     */
    private function applyDateScope($query, $range, $column)
    {
        match ($range) {
            'today'     => $query->whereDate($column, today()),
            'yesterday' => $query->whereDate($column, today()->subDay()),
            'week'      => $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]),
            'month'     => $query->whereMonth($column, now()->month)->whereYear($column, now()->year),
            default     => null,
        };
    }

    /**
     * AJAX: Get Counts (Optimized Single Query).
     */
   public function getLeadsCounts(Request $request)
{
    // 1. Get Base Query & Apply Standard Filters
    $baseQuery = $this->getBaseQuery();
    $baseQuery = $this->applyFilters($baseQuery, $request);

    // 2. Check Mode
    $isFollowUpMode = ($request->status === 'Follow-up Taken');

    if ($isFollowUpMode) {
        // A. Join followups to check dates
        // We use inner join because if status is 'Follow-up Taken', a followup MUST exist.
        $baseQuery->join('followups', 'leads.id', '=', 'followups.lead_id');

        // REMOVED: $baseQuery->whereRaw(...) 
        // We removed the 'max(id)' filter. 
        // This allows us to see ALL follow-ups so we don't miss "Today" 
        // just because you created a "Tomorrow" follow-up afterwards.

        $dateCol = 'followups.next_followup_date';
    } else {
        $dateCol = 'leads.created_at';
    }

    // 3. Aggregation
    // COUNT(DISTINCT leads.id) is the magic here.
    // It ensures that even if a lead matches the date 5 times, it is only counted ONCE.
    $counts = $baseQuery->selectRaw("
        COUNT(DISTINCT leads.id) as all_count,
        COUNT(DISTINCT CASE WHEN DATE($dateCol) = CURDATE() THEN leads.id END) as today_count,
        COUNT(DISTINCT CASE WHEN DATE($dateCol) = SUBDATE(CURDATE(), 1) THEN leads.id END) as yesterday_count,
        COUNT(DISTINCT CASE WHEN YEARWEEK($dateCol, 1) = YEARWEEK(CURDATE(), 1) THEN leads.id END) as week_count,
        COUNT(DISTINCT CASE WHEN MONTH($dateCol) = MONTH(CURDATE()) AND YEAR($dateCol) = YEAR(CURDATE()) THEN leads.id END) as month_count
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
            'phone_number' => 'required|numeric|digits_between:7,15',
            'phone_code'   => 'required|numeric',
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
            'phone_number' => 'required|max:15',
            'phone_code'   => 'required|numeric',
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