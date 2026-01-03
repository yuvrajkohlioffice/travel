<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\Company;
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
            $data = PaymentMethod::with('company')->latest(); // eager load company

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('company', function ($row) {
                    return $row->company ? $row->company->company_name : 'No Company';
                })
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
                        '\' class="px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2 edit-btn">Edit</button>
                        <button class="px-3 py-1 bg-red-600 text-white rounded text-sm delete-btn" data-id="' .
                        $row->id .
                        '">Delete</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $companies = Company::all(); // for modal dropdown
        return view('payment-methods.index', compact('companies'));
    }

    /* =============================
       STORE / UPDATE
    ============================== */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,online,wallet',
            'company_id' => 'required|exists:companies,id',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        PaymentMethod::updateOrCreate(['id' => $request->id], $request->all());

        return response()->json([
            'status' => true,
            'message' => $request->id ? 'Payment Method Updated Successfully' : 'Payment Method Created Successfully',
        ]);
    }
public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

       $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash,online,wallet',
            'company_id' => 'required|exists:companies,id',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
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
