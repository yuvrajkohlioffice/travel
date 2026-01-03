<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaymentMethodController extends Controller
{
    /* =============================
       INDEX
    ============================== */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PaymentMethod::latest();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('tax', function ($row) {
                    return $row->is_tax_applicable ? "{$row->tax_percentage}% ({$row->tax_name})" : 'No';
                })

                ->addColumn('status', function ($row) {
                    return $row->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                })

                ->addColumn('action', function ($row) {
                    return '
                        <button data-row=\'' .
                        json_encode($row) .
                        '\'
                            class=" px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2 edit-btn"
                            >
                            Edit
                        </button>

                        <button
                            class="px-3 py-1 bg-red-600 text-white rounded text-sm delete-btn"
                            data-id="' .
                        $row->id .
                        '">
                            Delete
                        </button>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('payment-methods.index');
    }

    /* =============================
       STORE
    ============================== */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,online,wallet',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        PaymentMethod::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Payment Method Created Successfully',
        ]);
    }

    /* =============================
       UPDATE
    ============================== */
    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,online,wallet',
        ]);

        $paymentMethod->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Payment Method Updated Successfully',
        ]);
    }

    /* =============================
       DELETE
    ============================== */
    public function destroy($id)
    {
        PaymentMethod::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment Method Deleted',
        ]);
    }
}
