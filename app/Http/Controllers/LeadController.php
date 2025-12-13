<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function showJson(Lead $lead)
    {
        return response()->json($lead->only([
            'id', 'name', 'company_name', 'email', 'country', 'district', 'phone_code',
            'phone_number', 'city', 'client_category', 'lead_status', 'lead_source',
            'website', 'status', 'package_id', 'inquiry_text', 'people_count', 'child_count'
        ]));
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Fetch required users and packages once
        $users = User::select('id', 'name')->get();
        $packages = Package::select('id', 'package_name')->orderBy('package_name')->get();

        // Filters
        $filters = $request->only([
            'country', 'district', 'city', 'lead_status', 'status', 'package_id', 
            'user_id', 'assigned_to', 'time'
        ]);

        $filters['time'] = $filters['time'] ?? 'all';

        // Base query
        $baseQuery = Lead::query()->select([
            'id','name','company_name','email','phone_number','status','lead_status','package_id','created_at'
        ])->when($user->role_id != 1, fn($q) => 
            $q->where(fn($q2) => $q2->where('user_id', $user->id)
                ->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id)))
        );

        if ($user->role_id == 1) $baseQuery->withTrashed();

        // Apply filters dynamically
        foreach (['country','district','city','lead_status','package_id','user_id'] as $key) {
            if (!empty($filters[$key])) $baseQuery->where($key, $filters[$key]);
        }

        if (!empty($filters['assigned_to'])) {
            $baseQuery->whereHas('assignedUsers', fn($q) => $q->where('user_id', $filters['assigned_to']));
        }

        // Time-based counts
        $timeCounts = [
            'all' => (clone $baseQuery)->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'week' => (clone $baseQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => (clone $baseQuery)->whereMonth('created_at', now()->month)
                                       ->whereYear('created_at', now()->year)->count(),
        ];

        // Status counts using single DB query
        $statusList = ['Pending','Approved','Quotation Sent','Follow-up Taken','Converted','Lost','On Hold','Rejected'];
        $statusCountsRaw = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusOthersCounts = array_merge(['All' => $timeCounts['all']], array_fill_keys($statusList, 0));
        foreach ($statusCountsRaw as $status => $count) $statusOthersCounts[$status] = $count;

        // Main paginated query with eager loading to prevent N+1
        $leads = (clone $baseQuery)
            ->with([
                'package:id,package_name',
                'latestAssignedUser.user:id,name',
                'latestAssignedUser.assignedBy:id,name',
                'createdBy:id,name',
                'lastFollowup.user:id,name',
                'invoice:id,invoice_no,lead_id,final_price'
            ])
            ->when($filters['status'] ?? null, fn($q,$v) => $q->where('status', $v))
            ->when($filters['time'] != 'all', function($q) use ($filters) {
                $period = match($filters['time']) {
                    'today' => [now()->startOfDay(), now()->endOfDay()],
                    'week' => [now()->startOfWeek(), now()->endOfWeek()],
                    'month' => [now()->startOfMonth(), now()->endOfMonth()],
                    default => null
                };
                if ($period) $q->whereBetween('created_at', $period);
            })
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('leads.index', compact('leads', 'packages', 'users', 'filters', 'statusOthersCounts', 'timeCounts'));
    }

    public function getLeadsData(Request $request)
    {
        $user = auth()->user();

        $query = Lead::with([
            'package:id,package_name',
            'latestAssignedUser.user:id,name',
            'latestAssignedUser.assignedBy:id,name',
            'createdBy:id,name',
            'lastFollowup.user:id,name'
        ])->orderByDesc('created_at');

        if ($user->role_id == 1) $query->withTrashed();
        if ($user->role_id != 1) $query->where(fn($q) => $q->where('user_id', $user->id)
                        ->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id)));

        // Apply search filters efficiently
        foreach (['id','status','lead_status'] as $field) {
            if ($request->filled($field)) $query->where($field, 'like', '%' . $request->$field . '%');
        }

        if ($request->filled('client_name')) {
            $query->where(fn($q) => 
                $q->where('name','like','%'.$request->client_name.'%')
                  ->orWhere('email','like','%'.$request->client_name.'%')
                  ->orWhere('phone_number','like','%'.$request->client_name.'%')
            );
        }

        if ($request->filled('location')) {
            $query->where(fn($q) => 
                $q->where('country','like','%'.$request->location.'%')
                  ->orWhere('district','like','%'.$request->location.'%')
                  ->orWhere('city','like','%'.$request->location.'%')
            );
        }

        if ($request->filled('date_range')) {
            $query->where(function($q) use ($request) {
                match($request->date_range) {
                    'today' => $q->whereDate('created_at', today()),
                    'yesterday' => $q->whereDate('created_at', today()->subDay()),
                    'week' => $q->whereBetween('created_at',[now()->startOfWeek(), now()->endOfWeek()]),
                    'month' => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                    default => null
                };
            });
        }

        if ($request->filled('assigned')) {
            $query->whereHas('latestAssignedUser.user', fn($uq) => $uq->where('name', $request->assigned));
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', fn($lead) => '<input type="checkbox" value="' . $lead->id . '" class="h-4 w-4 text-gray-700 border-gray-400">')
            ->addColumn('client_info', fn($lead) => view('leads.partials.client_info', compact('lead'))->render())
            ->addColumn('location', fn($lead) => $lead->country.'<br>'.$lead->district.'<br>'.$lead->city)
            ->addColumn('reminder', fn($lead) => view('leads.partials.reminder', compact('lead'))->render())
            ->addColumn('inquiry', fn($lead) => $lead->package->package_name ?? Str::limit($lead->inquiry_text, 20))
            ->addColumn('proposal', fn($lead) => view('leads.partials.proposal', compact('lead'))->render())
            ->addColumn('status', fn($lead) => view('leads.partials.status_dropdown', compact('lead'))->render())
            ->addColumn('assigned', fn($lead) => view('leads.partials.assigned', compact('lead'))->render())
            ->addColumn('action', fn($lead) => view('leads.partials.actions', compact('lead'))->render())
            ->rawColumns(['checkbox','client_info','location','reminder','inquiry','proposal','status','assigned','action'])
            ->make(true);
    }

    public function getLeadsCounts(Request $request)
    {
        $user = auth()->user();

        $query = Lead::query()
            ->when($user->role_id != 1, fn($q) => $q->where(fn($q2) => 
                $q2->where('user_id', $user->id)
                   ->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id))
            ))
            ->when($request->id, fn($q) => $q->where('id', $request->id))
            ->when($request->client_name, fn($q) => $q->where('name', 'like', "%{$request->client_name}%"))
            ->when($request->location, fn($q) => $q->where(fn($q2) => 
                $q2->where('country', 'like', "%{$request->location}%")
                   ->orWhere('district', 'like', "%{$request->location}%")
                   ->orWhere('city', 'like', "%{$request->location}%")
            ))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->lead_status, fn($q) => $q->where('lead_status', $request->lead_status))
            ->when($request->assigned, fn($q) => $q->whereHas('latestAssignedUser.user', fn($uq) => $uq->where('name', $request->assigned)));

        $periods = [
            'today' => today(),
            'yesterday' => today()->subDay(),
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
        ];

        $counts = [];
        foreach ($periods as $key => $range) {
            $q = clone $query;
            if (is_array($range)) $q->whereBetween('created_at', $range);
            else $q->whereDate('created_at', $range);
            $counts[$key] = $q->count();
        }
        $counts['all'] = $query->count();

        return response()->json($counts);
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
        // Soft delete related assigned users (if the relationship exists)
        if ($lead->assignedUsers()->exists()) {
            $lead->assignedUsers()->delete(); // soft delete if assignedUsers uses SoftDeletes
        }

        // Soft delete the lead itself
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
            'lead_ids.*' => 'integer|exists:leads,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $leadIds = $request->lead_ids;
        $userId = $request->user_id;
        $assignedBy = auth()->id();

        // Step 1: Get already assigned leads to this user
        $alreadyAssigned = LeadUser::whereIn('lead_id', $leadIds)->where('user_id', $userId)->pluck('lead_id')->toArray();

        // Step 2: Filter unassigned leads
        $toAssign = array_diff($leadIds, $alreadyAssigned);

        if (count($toAssign) > 0) {
            $insertData = [];
            $now = now();

            foreach ($toAssign as $leadId) {
                $insertData[] = [
                    'lead_id' => $leadId,
                    'user_id' => $userId,
                    'assigned_by' => $assignedBy,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Bulk insert (fast)
            LeadUser::insert($insertData);
        }

        return response()->json([
            'success' => true,
            'assigned_count' => count($toAssign),
            'skipped' => count($alreadyAssigned),
            'message' => 'Selected leads assigned successfully!',
        ]);
    }
}
