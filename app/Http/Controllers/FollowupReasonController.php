<?php

namespace App\Http\Controllers;

use App\Models\FollowupReason;
use App\Models\Company;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class FollowupReasonController extends Controller
{
    public function indexApi(Request $request)
    {
        $companyId = Auth::user()?->company_id;

        $reasons = FollowupReason::active()
            ->when(
                $companyId,
                function ($q) use ($companyId) {
                    $q->where(function ($sub) use ($companyId) {
                        $sub->where('company_id', $companyId)->orWhere('is_global', true);
                    });
                },
                function ($q) {
                    $q->where('is_global', true);
                },
            )
            ->orderBy('name')
            ->get(['id', 'name', 'remark', 'date', 'time', 'email_template', 'whatsapp_template', 'lead_status_id']);

        return response()->json([
            'success' => true,
            'data' => $reasons,
        ]);
    }

    // Display Followup Reasons (DataTable)
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $query = $user->role_id == 1 ? FollowupReason::with('company') : FollowupReason::where('company_id', $user->company_id)->orWhere('is_global', true)->with('company');

            return DataTables::of($query)
                ->addColumn('company', fn($row) => $row->company?->company_name ?? '-')
                ->addColumn('remark', fn($row) => $row->remark ? 'Yes' : 'No')
                ->addColumn('date', fn($row) => $row->date ? 'Yes' : 'No')
                ->addColumn('lead_status', fn($row) => $row->leadStatus?->name ?? '-')

                ->addColumn('time', fn($row) => $row->time ? 'Yes' : 'No')
                ->addColumn('is_active', fn($row) => $row->is_active ? 'Active' : 'Inactive')
                ->addColumn('is_global', fn($row) => $row->is_global ? 'Yes' : 'No')
                ->addColumn('action', function ($row) {
                    $data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    $btn =
                        '<button type="button" x-data x-on:click="$dispatch(\'edit-followup\', ' .
                        $data .
                        ')"
                                class="px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2">Edit</button>';
                    $btn .=
                        '<button type="button" data-id="' .
                        $row->id .
                        '"
                                class="delete-btn px-3 py-1 bg-red-600 text-white rounded text-sm">Delete</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $companies = $user->role_id == 1 ? Company::all() : Company::where('id', $user->company_id)->get();
        $leadStatuses = LeadStatus::all();

        return view('followup_reasons.index', compact('companies', 'user','leadStatuses'));
    }

    // Store new FollowupReason
    public function store(Request $request)
    {
        $this->validateBooleanFields($request);

        $reason = FollowupReason::create([
            'company_id' => $request->is_global ? null : $request->company_id ?? Auth::id(),
            'name' => $request->name,
            'remark' => $request->remark,
            'lead_status_id' => $request->lead_status_id,
            'date' => $request->date,
            'time' => $request->time,
            'email_template' => $request->email_template,
            'whatsapp_template' => $request->whatsapp_template,
            'is_active' => $request->is_active,
            'is_global' => $request->is_global,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Followup Reason saved successfully',
            'data' => $reason,
        ]);
    }

    // Update existing FollowupReason
    public function update(Request $request, $id)
    {
        $this->validateBooleanFields($request, $id);

        $reason = FollowupReason::findOrFail($id);

        $reason->update([
            'company_id' => $request->is_global ? null : $request->company_id ?? Auth::user()->company_id,
            'lead_status_id' => $request->lead_status_id,
            'name' => $request->name,
            'remark' => $request->remark,
            'date' => $request->date,
            'time' => $request->time,
            'email_template' => $request->email_template,
            'whatsapp_template' => $request->whatsapp_template,
            'is_active' => $request->is_active,
            'is_global' => $request->is_global,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Followup Reason updated successfully',
            'data' => $reason,
        ]);
    }

    // Delete FollowupReason
    public function destroy($id)
    {
        $user = Auth::user();
        $reason = FollowupReason::findOrFail($id);

        if ($reason->company_id !== $user->company_id && $user->role_id != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $reason->delete();

        return response()->json([
            'status' => true,
            'message' => 'Followup Reason deleted successfully',
        ]);
    }

    // Common method to validate boolean fields
    protected function validateBooleanFields(Request $request, $id = null)
    {
        $request->merge([
            'remark' => filter_var($request->remark, FILTER_VALIDATE_BOOLEAN),
            'date' => filter_var($request->date, FILTER_VALIDATE_BOOLEAN),
            'time' => filter_var($request->time, FILTER_VALIDATE_BOOLEAN),
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN),
            'is_global' => filter_var($request->is_global, FILTER_VALIDATE_BOOLEAN),
        ]);

        $rules = [
            'company_id' => 'nullable|exists:companies,id',
            'lead_status_id' => 'nullable|exists:lead_statuses,id',
            'name' => 'required|string|unique:followup_reasons,name' . ($id ? ",$id" : ''),
            'remark' => 'required|boolean',
            'date' => 'required|boolean',
            'time' => 'required|boolean',
            'email_template' => 'nullable|string',
            'whatsapp_template' => 'nullable|string',
            'is_active' => 'required|boolean',
            'is_global' => 'required|boolean',
        ];

        $request->validate($rules);
    }
}
