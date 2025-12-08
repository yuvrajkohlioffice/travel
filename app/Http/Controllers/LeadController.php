<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Package;
use App\Models\User;
use App\Models\LeadUser;
use Illuminate\Http\Request;
use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

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

        // Base query with filters
        $baseQuery = Lead::select('id', 'name', 'company_name', 'email', 'phone_number', 'status', 'lead_status', 'package_id', 'created_at')
            ->when($user->role_id != 1, fn($q) => $q->where(fn($q2) => $q2->where('user_id', $user->id)->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id))))
            ->when($filters['country'], fn($q, $v) => $q->where('country', $v))
            ->when($filters['district'], fn($q, $v) => $q->where('district', $v))
            ->when($filters['city'], fn($q, $v) => $q->where('city', $v))
            ->when($filters['lead_status'], fn($q, $v) => $q->where('lead_status', $v))
            ->when($filters['package_id'], fn($q, $v) => $q->where('package_id', $v))
            ->when($filters['user_id'], fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['assigned_to'], fn($q, $v) => $q->whereHas('assignedUsers', fn($uq) => $uq->where('user_id', $v)));

        // Apply status filter for time counts
        $timeBase = (clone $baseQuery)->when($filters['status'], fn($q, $v) => $q->where('status', $v));

        $timeCounts = [
            'all' => (clone $timeBase)->count(),
            'today' => (clone $timeBase)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'week' => (clone $timeBase)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => (clone $timeBase)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        // Status counts
        $statusCounts = [
            'All' => (clone $baseQuery)->count(),
            'Follow-up Taken' => (clone $baseQuery)->where('status', 'Follow-up Taken')->count(),
            'Converted' => (clone $baseQuery)->where('status', 'Converted')->count(),
            'Approved' => (clone $baseQuery)->where('status', 'Approved')->count(),
            'Rejected' => (clone $baseQuery)->where('status', 'Rejected')->count(),
        ];

        // Main query with pagination
        $leadsQuery = $baseQuery
            ->with(['package:id,package_name'])
            ->when($filters['status'], fn($q, $v) => $q->where('status', $v))
            ->when($filters['time'] != 'all', function ($q) use ($filters) {
                if ($filters['time'] == 'today') {
                    $q->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                } elseif ($filters['time'] == 'week') {
                    $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                } elseif ($filters['time'] == 'month') {
                    $q->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                }
            })
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
            ->latest('id');

        // Use pagination instead of get()
        $leads = $leadsQuery->paginate(50)->withQueryString(); // 50 per page

        return view('leads.index', compact('leads', 'packages', 'users', 'filters', 'statusCounts', 'timeCounts'));
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
    ])->when($user->role_id != 1, fn($q) => $q->where(fn($q2) =>
        $q2->where('user_id', $user->id)
           ->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id))
    ));

    return DataTables::of($query)
        ->addIndexColumn()
        ->addColumn('checkbox', fn($lead) => '<input type="checkbox" class="h-4 w-4" value="'.$lead->id.'">')
        ->addColumn('client_info', function($lead){
            $maskedPhone = str_repeat('*', strlen($lead->phone_number)-4) . substr($lead->phone_number,-4);
            $statusClass = [
                'Hot' => 'bg-red-500',
                'Warm' => 'bg-yellow-400',
                'Cold' => 'bg-gray-400',
                'Interested' => 'bg-green-500'
            ][$lead->lead_status] ?? 'bg-gray-300';

            return view('leads.columns.client_info', compact('lead','maskedPhone','statusClass'))->render();
        })
        ->addColumn('location', fn($lead) => $lead->country.'<br>'.$lead->district.'<br>'.$lead->city)
        ->addColumn('reminder', function($lead){
            return $lead->lastFollowup 
                ? '<strong>Last:</strong> '.$lead->lastFollowup->reason.'<br><strong>By:</strong> '.$lead->lastFollowup->user->name 
                : '';
        })
        ->addColumn('inquiry', fn($lead) => $lead->package->package_name ?? \Str::limit($lead->inquiry_text, 20))
        ->addColumn('proposal', function($lead){
            return view('leads.columns.proposal_buttons', compact('lead'))->render();
        })
        ->addColumn('status', function($lead){
            $stageClass = [
                'Pending'=>'bg-blue-400 text-white',
                'Approved'=>'bg-green-500 text-white',
                'Quotation Sent'=>'bg-indigo-500 text-white',
                'Follow-up Taken'=>'bg-purple-500 text-white',
                'Converted'=>'bg-teal-500 text-white',
                'Lost'=>'bg-gray-500 text-white',
                'On Hold'=>'bg-orange-400 text-white',
                'Rejected'=>'bg-red-600 text-white'
            ][$lead->status] ?? 'bg-gray-300 text-white';

            return '<span class="'.$stageClass.' px-2 py-1 rounded text-xs">'.$lead->status.'</span>';
        })
        ->addColumn('assigned', fn($lead) => $lead->latestAssignedUser->user->name ?? 'N/A')
        ->addColumn('action', function($lead){
            return view('leads.columns.actions', compact('lead'))->render();
        })
        ->rawColumns(['checkbox','client_info','location','reminder','inquiry','proposal','status','assigned','action'])
        ->make(true);
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
