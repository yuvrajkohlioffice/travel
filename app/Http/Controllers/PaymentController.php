<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Get all payments for AJAX (optionally by invoice)
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
        // Validate request
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'paid_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'next_payment_date' => 'nullable|date',
            'reminder_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        // Fetch invoice and lead
        $invoice = Invoice::findOrFail($request->invoice_id);
        $lead = $invoice->lead; // assuming Invoice belongsTo Lead

        // Calculate remaining amount and payment status
        $remaining = max($request->amount - $request->paid_amount, 0);
        $status = $request->paid_amount == 0 ? 'pending' : ($request->paid_amount < $request->amount ? 'partial' : 'completed');

        // Create Payment
        $payment = Payment::create([
            'invoice_id' => $request->invoice_id,
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'paid_amount' => $request->paid_amount,
            'remaining_amount' => $remaining,
            'status' => $status,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
            'next_payment_date' => $request->next_payment_date,
            'reminder_date' => $request->reminder_date,
        ]);

        // Update Lead status based on payment
        if ($lead) {
            if ($status === 'completed') {
                $lead->update(['lead_status' => 'paid']);
            } elseif ($status === 'partial') {
                $lead->update(['lead_status' => 'partial']);
            } else {
                $lead->update(['lead_status' => 'pending']);
            }
        }

        // Create follow-up if next_payment_date is provided
        if ($request->next_payment_date && $lead) {
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
        ]);
    }

    /**
     * Update an existing payment (for partial/full updates)
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
            'next_payment_date' => 'nullable|date',
            'reminder_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        if ($request->has('paid_amount')) {
            $payment->recordPayment($request->paid_amount, $request->payment_method, $request->transaction_id, $request->notes);
        } else {
            $payment->update($request->only(['payment_method', 'transaction_id', 'notes', 'next_payment_date', 'reminder_date']));
        }

        return response()->json([
            'status' => 'success',
            'payment' => $payment,
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
     * Get payments that need reminders (for cron/AJAX check)
     */
    public function reminders()
    {
        $payments = Payment::with('invoice')->where('status', 'partial')->whereDate('reminder_date', '<=', now())->get();

        return response()->json([
            'status' => 'success',
            'payments' => $payments,
        ]);
    }
}
