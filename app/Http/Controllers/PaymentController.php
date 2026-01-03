<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PaymentController extends Controller
{
    /**
     * Datatable View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Payment::with(['invoice', 'user'])->latest();

            return DataTables::of($query)
                ->addColumn('invoice_no', fn($row) => $row->invoice?->invoice_no ?? '-')
                ->addColumn('paid_amount', fn($row) => '₹' . number_format($row->paid_amount, 2))
                ->addColumn('remaining_amount', fn($row) => '₹' . number_format($row->remaining_amount, 2))
                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'paid' => 'text-green-600',
                        'partial' => 'text-yellow-600',
                        default => 'text-red-600',
                    };
                    return "<span class='{$color} font-semibold'>" . ucfirst($row->status) . "</span>";
                })
                ->addColumn('action', function ($row) {
                    $data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                    return '
                        <button x-data x-on:click="$dispatch(\'edit-payment\', ' . $data . ')"
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2">Edit</button>

                        <button data-id="' . $row->id . '"
                            class="delete-btn px-3 py-1 bg-red-600 text-white rounded text-sm">
                            Delete
                        </button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $invoices = Invoice::select('id', 'invoice_no')->get();

        return view('payments.index', compact('invoices'));
    }

    /**
     * Store Payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'paid_amount' => 'required|numeric|min:1',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('paid_amount') + $request->paid_amount;
        $remaining = max($invoice->final_price - $totalPaid, 0);

        $status = $totalPaid >= $invoice->final_price ? 'paid' : 'partial';

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => Auth::id(),
            'amount' => $invoice->final_price,
            'paid_amount' => $request->paid_amount,
            'remaining_amount' => $remaining,
            'status' => $status,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Payment saved successfully',
            'data' => $payment,
        ]);
    }

    /**
     * Update Payment
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update($request->only([
            'paid_amount',
            'payment_method',
            'notes',
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Payment updated successfully',
        ]);
    }

    /**
     * Delete Payment
     */
    public function destroy($id)
    {
        Payment::findOrFail($id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Payment deleted successfully',
        ]);
    }
}
