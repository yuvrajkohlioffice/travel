<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LeadStatusController extends Controller
{
    /**
     * API: Get lead statuses for dropdowns
     */
    public function indexApi(Request $request)
    {
        $companyId = Auth::user()?->company_id;

        $statuses = LeadStatus::where('is_active', true)
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
            ->orderBy('order_by')
            ->orderBy('name')
            ->get(['id', 'name', 'color', 'order_by', 'is_global']);

        return response()->json([
            'success' => true,
            'data' => $statuses,
        ]);
    }

    /**
     * Datatable View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companies = Company::all();

        if ($request->ajax()) {
            $query =
                $user->role_id == 1
                    ? LeadStatus::query()
                    : LeadStatus::where(function ($q) use ($user) {
                        $q->where('company_id', $user->company_id)->orWhere('is_global', true);
                    });

            return DataTables::of($query)
                ->addColumn('is_active', fn($row) => $row->is_active ? '<span class="text-green-600 font-semibold">Active</span>' : '<span class="text-red-600 font-semibold">Inactive</span>')
                ->addColumn('is_global', fn($row) => $row->is_global ? 'Yes' : 'No')
                ->addColumn('color', fn($row) => '<span class="px-2 py-1 rounded text-white ' . $row->color . '">' . $row->color . '</span>')
                ->addColumn('action', function ($row) use ($user) {
                    if ($row->is_global && $user->role_id != 1) {
                        return '<span class="text-gray-400 text-sm">No actions</span>';
                    }

                    $data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                    // DataTable edit button
                    $btn =
                        '<button type="button" x-data x-on:click="$dispatch(\'edit-lead\', ' .
                        $data .
                        ')"
            class="px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2">Edit</button>';

                    $btn .=
                        '<button type="button"
                                data-id="' .
                        $row->id .
                        '"
                                class="delete-btn px-3 py-1 bg-red-600 text-white rounded text-sm">
                                Delete
                            </button>';

                    return $btn;
                })
                ->rawColumns(['action', 'is_active', 'color'])
                ->make(true);
        }

        return view('lead_statuses.index', compact('user', 'companies'));
    }

    /**
     * Store new Lead Status
     */
    public function store(Request $request)
    {
        $this->validateFields($request);

        $status = LeadStatus::create([
            'company_id' => $request->is_global ? null : Auth::user()->company_id,
            'name' => $request->name,
            'color' => $request->color,
            'order_by' => $request->order_by ?? 0,
            'is_active' => $request->is_active,
            'is_global' => $request->is_global,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Lead Status saved successfully',
            'data' => $status,
        ]);
    }

    /**
     * Update Lead Status
     */
    public function update(Request $request, $id)
    {
        $this->validateFields($request, $id);

        $status = LeadStatus::findOrFail($id);

        $status->update([
            'company_id' => $request->is_global ? null : Auth::user()->company_id, // ✅ set company
            'name' => $request->name,
            'color' => $request->color,
            'order_by' => $request->order_by ?? 0, // ✅ support order_by
            'is_active' => $request->is_active,
            'is_global' => $request->is_global,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Lead Status updated successfully',
            'data' => $status,
        ]);
    }

    /**
     * Delete Lead Status
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $status = LeadStatus::findOrFail($id);

        if ($status->is_global && $user->role_id != 1) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$status->is_global && $status->company_id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $status->delete();

        return response()->json([
            'status' => true,
            'message' => 'Lead Status deleted successfully',
        ]);
    }

    /**
     * Common validator
     */
    protected function validateFields(Request $request, $id = null)
    {
        $request->merge([
            'is_active' => filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN),
            'is_global' => filter_var($request->is_global, FILTER_VALIDATE_BOOLEAN),
        ]);

        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'required|string|unique:lead_statuses,name' . ($id ? ",$id" : ''),
            'color' => 'required|string',
            'order_by' => 'nullable|integer',
            'is_active' => 'required|boolean',
            'is_global' => 'required|boolean',
        ]);
    }
}
