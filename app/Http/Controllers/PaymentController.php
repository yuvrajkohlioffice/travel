<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Mail\PaymentReceivedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\LeadUser;
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

                // LOGIC FIX: Remaining Amount = Invoice Final Price - Sum of All Payments for that invoice
                ->addColumn('remaining_amount', function ($row) {
                    if (!$row->invoice) {
                        return '-';
                    }

                    // Calculate total paid for this specific invoice
                    $totalPaid = Payment::where('invoice_id', $row->invoice_id)->sum('paid_amount');
                    $remaining = $row->invoice->final_price - $totalPaid;

                    // Ensure we don't show negative values (in case of overpayment)
                    return '₹' . number_format(max($remaining, 0), 2);
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
                    // Safe JSON encoding for the Edit button
                    $data = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    return '
                <button x-data x-on:click="$dispatch(\'edit-payment\', ' .
                        $data .
                        ')"
                    class="px-3 py-1 bg-blue-600 text-white rounded text-sm mr-2">Edit</button>
                <button data-id="' .
                        $row->id .
                        '"
                    class="delete-btn px-3 py-1 bg-red-600 text-white rounded text-sm">Delete</button>
            ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        // --- START OF UPDATED DROPDOWN LOGIC ---

        $userId = Auth::id();
        $userRoleId = Auth::user()->role_id;

        $invoices = Invoice::select('id', 'invoice_no', 'final_price', 'lead_id')
            // 1. CALCULATE PENDING AMOUNT IN SQL
            // Formula: Final Price - (Sum of payments made)
            ->selectRaw('(final_price - (SELECT COALESCE(SUM(paid_amount), 0) FROM payments WHERE payments.invoice_id = invoices.id)) as pending_amount')

            // 2. FILTER: Only show invoices where Pending Amount is greater than 0
            ->having('pending_amount', '>', 0)

            // 3. ROLE ACCESS
            ->when($userRoleId !== 1, function ($query) use ($userId) {
                $query->whereIn('lead_id', function ($sub) use ($userId) {
                    $sub->select('lead_id')->from('lead_user')->where('user_id', $userId);
                });
            })
            ->get();

        // --- END OF UPDATED LOGIC ---

        $paymentMethods = \App\Models\PaymentMethod::all();

        return view('payments.index', compact('invoices', 'paymentMethods'));
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
        'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    DB::beginTransaction();

    try {
        $invoice = Invoice::with('lead')->lockForUpdate()->findOrFail($request->invoice_id);

        // Total already paid
        $alreadyPaid = Payment::where('invoice_id', $invoice->id)->sum('paid_amount');
        $totalPaid = $alreadyPaid + $request->paid_amount;

        if ($totalPaid > $invoice->final_price) {
            return response()->json([
                'status' => false,
                'message' => 'Paid amount exceeds invoice total',
            ], 422);
        }

        $remaining = $invoice->final_price - $totalPaid;
        $status = $remaining == 0 ? 'paid' : 'partial';

        // 1. Create Payment
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => Auth::id(), // The staff recording the payment
            'amount' => $invoice->final_price,
            'paid_amount' => $request->paid_amount,
            'status' => $status,
            'payment_method_id' => $request->payment_method_id,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
            'next_payment_date' => $status === 'partial' ? $request->next_payment_date : null,
            'reminder_date' => $status === 'partial' ? Carbon::parse($request->next_payment_date)->subDays(2) : null,
        ]);

        // 2. Upload Image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('payments', 'public');
            $payment->update(['image' => $path]);
        }

        DB::commit();

        // --- NOTIFICATIONS START ---
        
        // Get client details (Assuming email is on Lead or Invoice)
        // Adjust 'email' and 'phone' fields based on your actual Lead/Invoice table structure
        $clientEmail = $invoice->lead->email ?? $invoice->client_email; 
        

        // A. Send Email
        if ($clientEmail) {
            try {
                // Configure dynamic SMTP for the currently logged-in admin/staff
                setUserMailConfig(Auth::user());
                
                Mail::to($clientEmail)->send(new PaymentReceivedMail($payment, $remaining));
            } catch (\Exception $mailError) {
                // Log error but don't fail the transaction
                \Log::error("Payment Mail Error: " . $mailError->getMessage());
            }
        }

    
        // --- NOTIFICATIONS END ---

        return response()->json([
            'status' => true,
            'message' => 'Payment recorded and notification sent',
            'data' => $payment,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
            'error' => $e->getMessage(),
        ], 500);
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
