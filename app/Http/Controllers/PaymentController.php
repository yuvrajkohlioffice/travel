<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                ->addColumn('payment_method', fn($row) => $row->paymentMethod?->name ?? '-')
                ->addColumn('remaining_amount', function ($row) {
                    $paid = Payment::where('invoice_id', $row->invoice_id)->sum('paid_amount');
                    return '₹' . number_format($row->amount - $paid, 2);
                })

                ->addColumn('status', function ($row) {
                    $color = match ($row->status) {
                        'paid' => 'text-green-600',
                        'partial' => 'text-yellow-600',
                        default => 'text-red-600',
                    };
                    return "<span class='{$color} font-semibold'>" . ucfirst($row->status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');

                    return '
                        <button x-data x-on:click="$dispatch(\'edit-payment\', ' .
                        $data .
                        ')"
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2">Edit</button>

                        <button data-id="' .
                        $row->id .
                        '"
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
            'payment_method_id' => 'required|exists:payment_methods,id',
            'next_payment_date' => 'nullable|date|after:today',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // ✅
        ]);

        DB::beginTransaction();

        try {
            $invoice = Invoice::lockForUpdate()->findOrFail($request->invoice_id);

            // Total already paid
            $alreadyPaid = Payment::where('invoice_id', $invoice->id)->sum('paid_amount');
            $totalPaid = $alreadyPaid + $request->paid_amount;

            if ($totalPaid > $invoice->final_price) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Paid amount exceeds invoice total',
                    ],
                    422,
                );
            }

            $remaining = $invoice->final_price - $totalPaid;
            $status = $remaining == 0 ? 'paid' : 'partial';

            // ✅ Create payment FIRST
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => Auth::id(),
                'amount' => $invoice->final_price,
                'paid_amount' => $request->paid_amount,
                'status' => $status,
                'payment_method_id' => $request->payment_method_id,
                'transaction_id' => $request->transaction_id,
                'notes' => $request->notes,
                'next_payment_date' => $status === 'partial' ? $request->next_payment_date : null,
                'reminder_date' => $status === 'partial' ? Carbon::parse($request->next_payment_date)->subDays(2) : null,
            ]);

            // ✅ Upload image AFTER payment exists
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('payments', 'public');
                $payment->update(['image' => $path]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Payment recorded successfully',
                'data' => $payment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage(), // remove in production
                ],
                500,
            );
        }
    }

    /**
     * Update Payment
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $payment->update($request->only(['paid_amount', 'payment_method', 'notes']));

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
