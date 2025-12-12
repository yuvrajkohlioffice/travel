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
use Illuminate\Support\Str;
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
        $baseQuery = Lead::query();

        // Include soft deleted only for role_id = 1
        if ($user->role_id == 1) {
            $baseQuery = $baseQuery->withTrashed();
        }

        $baseQuery = $baseQuery
            ->select('id', 'name', 'company_name', 'email', 'phone_number', 'status', 'lead_status', 'package_id', 'created_at')
            ->when($user->role_id != 1, fn($q) => $q->where(fn($q2) => $q2->where('user_id', $user->id)->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id))))
            ->when($filters['country'], fn($q, $v) => $q->where('country', $v))
            ->when($filters['district'], fn($q, $v) => $q->where('district', $v))
            ->when($filters['city'], fn($q, $v) => $q->where('city', $v))
            ->when($filters['lead_status'], fn($q, $v) => $q->where('lead_status', $v))
            ->when($filters['package_id'], fn($q, $v) => $q->where('package_id', $v))
            ->when($filters['user_id'], fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['assigned_to'], fn($q, $v) => $q->whereHas('assignedUsers', fn($uq) => $uq->where('user_id', $v)));

        // Time-based counts
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
        $statusList = [
            'Pending',
            'Approved',
            'Quotation Sent',
            'Follow-up Taken',
            'Converted',
            'Lost',
            'On Hold',
            'Rejected',
        ];

        $statusOthersCounts = [];

        // Add ALL first
        $statusOthersCounts['All'] = (clone $baseQuery)->count();

        // Add other statuses
        foreach ($statusList as $status) {
            $statusOthersCounts[$status] = (clone $baseQuery)->where('status', $status)->count();
        }


        // Main query with relationships and filters
        $leadsQuery = (clone $baseQuery)
            ->with([
                'package:id,package_name',
                'invoice:id,invoice_no,lead_id', // eager load invoice
            ])
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
                "CASE
                WHEN lead_status = 'Hot' THEN 1
                WHEN lead_status = 'Warm' THEN 2
                WHEN lead_status = 'Cold' THEN 3
                ELSE 4
            END",
            )
            ->latest('id');

        // Pagination
        $leads = $leadsQuery->paginate(50)->withQueryString();

        return view('leads.index', compact('leads', 'packages', 'users', 'filters', 'statusCounts', 'statusOthersCounts', 'timeCounts'));
    }

    public function getLeadsData(Request $request)
    {
        $user = auth()->user();

        // Base query with relationships
        $query = Lead::with(['package:id,package_name', 'latestAssignedUser.user:id,name', 'latestAssignedUser.assignedBy:id,name', 'createdBy:id,name', 'lastFollowup.user:id,name']);

        // Include soft-deleted leads for role_id 1
        if ($user->role_id == 1) {
            $query = $query->withTrashed();
        }

        // Apply role-based restrictions for other users
        if ($user->role_id != 1) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id));
            });
        }
        if ($request->filled('id')) {
            $query->where('id', 'like', '%' . $request->id . '%');
        }

        if ($request->filled('client_name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->client_name . '%')
                    ->orWhere('email', 'like', '%' . $request->client_name . '%')
                    ->orWhere('phone_number', 'like', '%' . $request->client_name . '%');
            });
        }

        if ($request->filled('location')) {
            $query->where(function ($q) use ($request) {
                $q->where('country', 'like', '%' . $request->location . '%')
                    ->orWhere('district', 'like', '%' . $request->location . '%')
                    ->orWhere('city', 'like', '%' . $request->location . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range (created_at)
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', now());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
            }
        }

        if ($request->filled('assigned')) {
            $query->whereHas('latestAssignedUser.user', fn($q) => $q->where('name', $request->assigned));
        }
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('checkbox', fn($lead) => '<input type="checkbox" value="' . $lead->id . '" @change="toggleLead($event)" class="h-4 w-4 text-gray-700 border-gray-400">')
            ->addColumn('client_info', function ($lead) {

    // Mask phone
    $maskedPhone = str_repeat('*', strlen($lead->phone_number) - 4) . substr($lead->phone_number, -4);

    // --- Follow-up Expired Check ---
    $followup = $lead->latestFollowup;
    $followupText = '';


if ($followup) {
    $today = now()->startOfDay();
    $nextDate = \Carbon\Carbon::parse($followup->next_followup_date)->startOfDay();

    // Only show if expired
    if ($nextDate->isPast() && !$nextDate->isToday()) {
        $daysLate = $nextDate->diffInDays($today);
        $followupText = '<span class="text-white font-bold">Last followup expired: '.$daysLate.' days ago</span>';
    }
}


    // Lead status badge color
    $statusClass = [
        'Hot' => 'bg-red-500',
        'Warm' => 'bg-yellow-400',
        'Cold' => 'bg-gray-400',
        'Interested' => 'bg-green-500',
    ][$lead->lead_status] ?? 'bg-gray-300 text-black font-extrabold';

    // Days difference for created_at
    $createdDate = \Carbon\Carbon::parse($lead->created_at)->startOfDay();
    $today = now()->startOfDay();

    $days = $createdDate->diffInDays($today);
$shortName = Str::limit($lead->name, 11, '...');
    if ($days == 0) {
        $daysText = "Today";
    } elseif ($days == 1) {
        $daysText = "1 day ago";
    } else {
        $daysText = $days . " days ago";
    }

    return '
       <div class="font-xc flex items-center gap-2" title="' . htmlspecialchars($lead->name) . '">
    ' . $shortName . '
    | <span class="py-0.5 badge-custom rounded text-white font-bold ' . $statusClass . '">
        ' . ($lead->lead_status ?? 'N/A') . '
    </span>
    | <button @click="openEditModal(' . $lead->id . ')" class="text-gray-600 hover:text-black">
        <i class="fa-solid fa-pen-to-square"></i>
    </button>
</div>


        <div class="text-gray-600 text-sm font-mono">
            +' . $lead->phone_code . ' ' . $maskedPhone . '
        </div>

        <div class="text-gray-500"><span class=" text-xs text-black">Created At ' . $lead->created_at->format('d-M-y') . ' </span>
             |  
            <span class="bg-gray-500 text-black font-extrabold badge-custom"
                >
                ' . $daysText . '
            </span>
        </div>

        ' . ($followupText ? '
        <div class="text-xs font-semibold mt-1 badge-custom bg-red-600">
            <i class="fa-solid fa-clock-rotate-left mr-1"></i> ' . $followupText . '
        </div>' : '') . '
    ';
})

            ->addColumn('location', fn($lead) => $lead->country . '<br>' . $lead->district . '<br>' . $lead->city)
            ->addColumn('reminder', function ($lead) {
                $last = $lead->lastFollowup ? '<div class="text-xs text-gray-600 mt-2"><strong>Last:</strong> ' . $lead->lastFollowup->reason . '<br><strong>By:</strong> ' . $lead->lastFollowup->user->name . '</div>' : '';
                return '<button @click="openFollowModal(' . $lead->id . ', \'' . $lead->name . '\')" class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm">Followup</button>' . $last;
            })
            ->addColumn('inquiry', fn($lead) => $lead->package->package_name ?? \Str::limit($lead->inquiry_text, 20))

            ->addColumn('proposal', function ($lead) {
                // Encode lead safely for handleShare()
                $leadJson = htmlspecialchars(
                    json_encode(
                        [
                            'id' => $lead->id,
                            'name' => $lead->name,
                            'email' => $lead->email,
                            'phone_code' => $lead->phone_code,
                            'phone_number' => $lead->phone_number,
                            'package_id' => $lead->package_id ?? null,
                            'people_count' => $lead->people_count ?? 1,
                            'child_count' => $lead->child_count ?? 0,
                        ],
                        JSON_HEX_APOS | JSON_HEX_QUOT,
                    ),
                    ENT_QUOTES,
                    'UTF-8',
                );

                // Package & Invoice details
                $packageId = $lead->package->id ?? '';
                $invoice = $lead->invoice ?? null;

                $paymentButton = '';
                if ($invoice) {
                    $invoiceId = $invoice->id;
                    $invoiceNo = $invoice->invoice_no ?? '';
                    $invoiceFinalPrice = (float) ($invoice->final_price ?? 0);

                    // Calculate remaining amount from payments table
                    $totalPaid = \DB::table('payments')->where('invoice_id', $invoiceId)->sum('paid_amount');

                    $remainingAmount = max($invoiceFinalPrice - $totalPaid, 0);

                    // Only show payment button if remaining amount > 0
                    if ($remainingAmount > 0) {
                        $invoiceJson = htmlspecialchars(
                            json_encode(
                                [
                                    'id' => $invoiceId,
                                    'invoice_no' => $invoiceNo,
                                    'amount' => $invoiceFinalPrice,
                                    'remaining_amount' => $remainingAmount,
                                ],
                                JSON_HEX_APOS | JSON_HEX_QUOT,
                            ),
                            ENT_QUOTES,
                            'UTF-8',
                        );

                        $paymentButton = <<<HTML
                        <button
                            @click='openPaymentModal({$invoiceJson})'
                            class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 ml-1"
                        >
                            <i class="fa-solid fa-money-bill-wave"></i> Add Payment
                        </button>
                        HTML;
                    }
                }

                // Render all buttons
                return <<<HTML
                <button
                    @click='handleShare({$leadJson})'
                    class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm"
                >
                    <i class="fa-solid fa-share"></i>
                </button>

                <button
                    @click="openInvoiceModal(
                        {$lead->id},
                        '{$lead->name}',
                        '{$lead->people_count}',
                        '{$lead->child_count}',
                        '{$packageId}',
                        '{$lead->email}'
                    )"
                    class="px-3 py-1 border border-gray-400 rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1"
                >
                    <i class="fa-solid fa-file-invoice"></i>
                </button>

                {$paymentButton}
                HTML;
            })

            ->addColumn('status', function ($lead) {
                $stageClass =
                    [
                        'Pending' => 'bg-lime-500 text-white', // Lime for Pending
                        'Approved' => 'bg-green-500 text-white', // Green for Approved
                        'Quotation Sent' => 'bg-indigo-500 text-white', // Indigo for Quotation Sent
                        'Follow-up Taken' => 'bg-purple-500 text-white', // Purple for Follow-up Taken
                        'Converted' => 'bg-teal-500 text-white', // Teal for Converted
                        'Lost' => 'bg-gray-500 text-white', // Gray for Lost
                        'On Hold' => 'bg-amber-500 text-white', // Amber for On Hold
                        'Rejected' => 'bg-red-500 text-white', // Red for Rejected
                    ][$lead->status] ?? 'bg-gray-300 text-black font-extrabold'; // Default gray-300
                // Default gray-300

                 return '
            <div x-data="{ open: false, value: \''.$lead->status.'\' }" class="relative">
                <div x-show="!open" @click="open = true" class="cursor-pointer text-xs px-2 py-1 rounded '.$stageClass.'">
                    <span x-text="value || \'Select Status\'"></span>
                </div>
                <select x-show="open" x-cloak @change="value = $event.target.value; open=false; updateStatus('.$lead->id.', value);" @click.outside="open=false" class="px-2 py-1 rounded text-xs border bg-white dark:bg-gray-800">
                    <option value="">Select Status</option>
                    '.collect(['Pending','Approved','Quotation Sent','Follow-up Taken','Lost','Converted','On Hold','Rejected'])
                        ->map(fn($status) => '<option value="'.$status.'"'.($lead->status==$status?' selected':'').'>'.$status.'</option>')
                        ->implode(' ').'
                </select>
            </div>';
            })
            ->addColumn('assigned', function ($lead) {
                return '
            <div><strong>Assigned:</strong> ' .
                    ($lead->latestAssignedUser->user->name ?? 'N/A') .
                    '</div>
            <div><strong>By:</strong> ' .
                    ($lead->latestAssignedUser->assignedBy->name ?? 'N/A') .
                    '</div>
            <div><strong>Created:</strong> ' .
                    ($lead->createdBy->name ?? 'System') .
                    '</div>';
            })
            ->addColumn('action', function ($lead) {
                return '
            <a href="' .
                    route('leads.show', $lead->id) .
                    '" class="px-3 py-1  rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1"><i class="fa-solid fa-eye"></i></a>
            <a href="' .
                    route('leads.assign.form', $lead->id) .
                    '" class="px-3 py-1  rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1"><i class="fa-solid fa-user-plus"></i></a>
            <form action="' .
                    route('leads.destroy', $lead->id) .
                    '" method="POST" onsubmit="return confirm(\'Delete this lead?\')" class="inline">
                ' .
                    csrf_field() .
                    method_field('DELETE') .
                    '
                <button type="submit" class="px-3 py-1  rounded text-gray-700 hover:bg-gray-200 transition text-sm ml-1">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>';
            })
            ->rawColumns(['checkbox', 'client_info', 'location', 'reminder', 'inquiry', 'proposal', 'status', 'assigned', 'action', 'date_range'])
            ->make(true);
    }

    // LeadController.php
    public function getLeadsCounts(Request $request)
    {
        $user = auth()->user();

        $query = Lead::query()
            ->when($user->role_id != 1, fn($q) => $q->where(fn($q2) => $q2->where('user_id', $user->id)->orWhereHas('assignedUsers', fn($uq) => $uq->where('user_id', $user->id))))
            ->when($request->id, fn($q) => $q->where('id', $request->id))
            ->when($request->client_name, fn($q) => $q->where('name', 'like', "%{$request->client_name}%"))
            ->when(
                $request->location,
                fn($q) => $q->where(function ($q2) use ($request) {
                    $q2->where('country', 'like', "%{$request->location}%")
                        ->orWhere('district', 'like', "%{$request->location}%")
                        ->orWhere('city', 'like', "%{$request->location}%");
                }),
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->assigned, fn($q) => $q->whereHas('latestAssignedUser.user', fn($uq) => $uq->where('name', $request->assigned)));

        return response()->json([
            'today' => (clone $query)->whereDate('created_at', today())->count(),
            'yesterday' => (clone $query)->whereDate('created_at', today()->subDay())->count(),
            'week' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
            'all' => (clone $query)->count(),
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
