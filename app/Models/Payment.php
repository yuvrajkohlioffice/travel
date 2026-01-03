<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'payment_method',
        'payment_method_id',
        'transaction_id',
        'notes',
        'next_payment_date',
        'reminder_date',
    ];

    protected $dates = [
        'next_payment_date',
        'reminder_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Relationship: Payment belongs to Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Relationship: Payment made/handled by a User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a payment
     *
     * @param float $amountPaid
     * @param string|null $paymentMethod
     * @param string|null $transactionId
     * @param string|null $notes
     * @return void
     */
    public function recordPayment(float $amountPaid, string $paymentMethod = null, string $transactionId = null, string $notes = null)
    {
        // Update paid amount
        $this->paid_amount += $amountPaid;

        // Update remaining amount
        $this->remaining_amount = max($this->amount - $this->paid_amount, 0);

        // Update status
        if ($this->paid_amount == 0) {
            $this->status = 'pending';
        } elseif ($this->paid_amount < $this->amount) {
            $this->status = 'partial';
        } else {
            $this->status = 'completed';
        }

        // Update payment method / transaction
        if ($paymentMethod) $this->payment_method = $paymentMethod;
        if ($transactionId) $this->transaction_id = $transactionId;
        if ($notes) $this->notes = $notes;

        // Automatically set next reminder if partial
        if ($this->status === 'partial' && !$this->next_payment_date) {
            $this->next_payment_date = Carbon::now()->addDays(7); // example: next installment due in 7 days
            $this->reminder_date = Carbon::now()->addDays(5); // reminder 2 days before
        }

        $this->save();
    }

    /**
     * Check if reminder is due
     */
    public function isReminderDue(): bool
    {
        return $this->reminder_date && Carbon::now()->gte($this->reminder_date) && $this->status === 'partial';
    }
}
