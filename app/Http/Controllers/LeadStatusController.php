<?php

namespace App\Http\Controllers;

use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LeadStatusController extends Controller
{
    // Display Lead Statuses (DataTable)
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            // Super Admin (role_id = 2) → see all
            if ($user->role_id == 1) {
                $query = LeadStatus::query();
            }
            // Others → company + global
            else {
                $query = LeadStatus::where(function ($q) use ($user) {
                    $q->where('company_id', $user->company_id)->orWhere('is_global', 1);
                });
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) use ($user) {
                    // 🚫 Non–super admin cannot edit/delete global statuses
                    if ($row->is_global && $user->role_id != 1) {
                        return '<span class="text-gray-400 text-sm">No actions</span>';
                    }

                    $leadData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                    $btn =
                        '<button type="button"
                            x-data
                            x-on:click="$dispatch(\'edit-lead\', ' .
                        $leadData .
                        ')"
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2">
                            Edit
                        </button>';

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
                ->editColumn('is_active', fn($row) => $row->is_active ? '<span class="text-green-600 font-semibold">Active</span>' : '<span class="text-red-600 font-semibold">Inactive</span>')
                ->editColumn('is_global', fn($row) => $row->is_global ? 'Yes' : 'No')
                ->editColumn('color', fn($row) => '<span class="' . $row->color . ' px-2 py-1 rounded text-white">' . $row->color . '</span>')
                ->rawColumns(['action', 'is_active', 'is_global', 'color'])
                ->make(true);
        }

        return view('lead_statuses.index');
    }

    // Store or update LeadStatus
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|unique:lead_statuses,name,' . $request->id,
            'color' => 'required|string',
            'is_active' => 'required',
            'is_global' => 'nullable',
        ]);

        LeadStatus::updateOrCreate(
            ['id' => $request->id],
            [
                'company_id' => $user->company_id,
                'name' => $request->name,
                'color' => $request->color,
                'is_active' => $request->boolean('is_active'), // ✅ FIX
                'is_global' => $request->boolean('is_global'), // ✅ FIX
            ],
        );

        return response()->json([
            'status' => true,
            'message' => 'Lead Status saved successfully',
        ]);
    }

    // Delete LeadStatus
    public function destroy($id)
    {
        $status = LeadStatus::findOrFail($id);
        $user = Auth::user();

        if (!$status->is_global && $status->company_id !== $user->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $status->delete();
        return response()->json([
            'status' => true,
            'message' => 'Lead Status deleted successfully',
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:lead_statuses,name,' . $id,
            'color' => 'required|string',
            'is_active' => 'required',
            'is_global' => 'nullable',
        ]);

        $status = LeadStatus::findOrFail($id);

        $status->update([
            'name' => $request->name,
            'color' => $request->color,
            'is_active' => $request->boolean('is_active'), // ✅ FIX
            'is_global' => $request->boolean('is_global'), // ✅ FIX
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Lead Status updated successfully',
        ]);
    }
}
