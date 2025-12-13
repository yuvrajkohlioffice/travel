<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Get all payments (optionally by invoice)
     */
    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'user']);

        if ($request->invoice_id) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'payments' => $payments,
        ]);
    }

    /**
     * Store a new payment or partial payment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'next_payment_date' => 'nullable|date',
            'next_payment_time' => 'nullable',
            'reminder_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $invoice = Invoice::findOrFail($request->invoice_id);
        $lead = $invoice->lead;

        // Total paid so far
        $totalPaidSoFar = Payment::where('invoice_id', $invoice->id)->sum('paid_amount');
        $newTotalPaid = $totalPaidSoFar + $request->paid_amount;

        // Remaining amount
        $remaining = max($invoice->final_price - $newTotalPaid, 0);

        // Determine payment & lead status
        if ($newTotalPaid == 0) {
            $paymentStatus = 'pending';
            $leadStatus = 'Pending';
        } elseif ($newTotalPaid < $invoice->final_price) {
            $paymentStatus = 'partial';
            $leadStatus = 'Pending';
        } else {
            $paymentStatus = 'paid';
            $leadStatus = 'Converted';
           
            $lead->update([
                'lead_status' => $paymentStatus,
                'status' => $leadStatus === 'Converted' ? 'Converted' : $lead->status,
            ]); // Lead is fully paid → Converted
        }

        // Create payment record
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'paid_amount' => $request->paid_amount,
            'remaining_amount' => $remaining,
            'status' => $paymentStatus,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
            'next_payment_date' => $request->next_payment_date,
            'reminder_date' => $request->reminder_date,
        ]);

        // Update lead status and status
        if ($lead) {
            $lead->update([
                'lead_status' => $paymentStatus,
                
            ]);
        }

        // Create follow-up if partial payment
        if ($paymentStatus === 'partial' && $request->next_payment_date && $lead) {
            Followup::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'reason' => 'Payment Follow-up',
                'remark' => 'Next payment scheduled',
                'next_followup_date' => $request->next_payment_date,
                'next_followup_time' => $request->next_payment_time ?? null,
                'last_followup_date' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'payment' => $payment,
            'remaining' => $remaining,
            'status_label' => $paymentStatus,
        ]);
    }

    /**
     * Update an existing payment
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $invoice = $payment->invoice;
        $lead = $invoice->lead;

        $validator = Validator::make($request->all(), [
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'next_payment_date' => 'nullable|date',
            'next_payment_time' => 'nullable',
            'reminder_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update payment fields
        if ($request->has('paid_amount')) {
            $payment->paid_amount = $request->paid_amount;
            $payment->amount = $request->amount ?? $payment->amount;
        }

        $payment->update($request->only([
            'payment_method',
            'transaction_id',
            'notes',
            'next_payment_date',
            'next_payment_time',
            'reminder_date',
        ]));

        // Recalculate total paid
        $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('paid_amount');
        $remaining = max($invoice->final_price - $totalPaid, 0);

        // Determine new status
        if ($totalPaid == 0) {
            $paymentStatus = 'pending';
            $leadStatus = 'Pending';
        } elseif ($totalPaid < $invoice->final_price) {
            $paymentStatus = 'partial';
            $leadStatus = 'Pending';
        } else {
            $paymentStatus = 'paid';
            $leadStatus = 'Converted';
        }

        $payment->update([
            'remaining_amount' => $remaining,
            'status' => $paymentStatus,
        ]);

        // Update lead status and status
        if ($lead) {
            $lead->update([
                'lead_status' => $leadStatus,
                'status' => $leadStatus === 'Converted' ? 'Converted' : $lead->status,
            ]);
        }

        // Create follow-up if partial
        if ($paymentStatus === 'partial' && $request->next_payment_date && $lead) {
            Followup::create([
                'lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'reason' => 'Payment Follow-up',
                'remark' => 'Next payment scheduled',
                'next_followup_date' => $request->next_payment_date,
                'next_followup_time' => $request->next_payment_time ?? null,
                'last_followup_date' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'payment' => $payment,
            'remaining' => $remaining,
            'status_label' => $paymentStatus,
        ]);
    }

    /**
     * Delete a payment
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment deleted successfully',
        ]);
    }

    /**
     * Get payments that need reminders
     */
    public function reminders()
    {
        $payments = Payment::with('invoice')
            ->where('status', 'partial')
            ->whereDate('reminder_date', '<=', now())
            ->get();

        return response()->json([
            'status' => 'success',
            'payments' => $payments,
        ]);
    }
}
