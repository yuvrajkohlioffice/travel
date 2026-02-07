<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'user_id', 'amount', 'paid_amount', 'remaining_amount',
        'status', 'image', 'payment_method', 'payment_method_id',
        'transaction_id', 'notes', 'next_payment_date', 'reminder_date',
    ];

    protected $casts = [
        'next_payment_date' => 'datetime',
        'reminder_date' => 'datetime',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     * Applies visibility logic automatically.
     */
    protected static function booted()
    {
        static::addGlobalScope('accessible', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // 1. If Role ID is 1 (Admin), they see everything.
                if ($user->role_id === 1) {
                    return;
                }

                // 2. Otherwise, filter by the user's company.
                // We join the invoices table to check the company of the creator or the lead assignment.
                $builder->whereHas('invoice.user', function ($query) use ($user) {
                    $query->where('company_id', $user->company_id);
                });
                
                // 3. Optional: Further restrict so standard users only see 
                // payments they created or payments for leads assigned to them.
               
                $builder->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('invoice', function($iq) use ($user) {
                          $iq->where('user_id', $user->id)
                             ->orWhere('lead_id', $user->id); // if lead_id refers to a user
                      });
                });
               
            }
        });
    }

    /* -----------------------------------------------------------------
     |  RELATIONSHIPS
     | ----------------------------------------------------------------- */

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /* -----------------------------------------------------------------
     |  LOGIC METHODS
     | ----------------------------------------------------------------- */

    /**
     * Record a payment and update status/balances
     */
    public function recordPayment(float $amountPaid, string $paymentMethod = null, string $transactionId = null, string $notes = null)
    {
        $this->paid_amount += $amountPaid;
        $this->remaining_amount = max($this->amount - $this->paid_amount, 0);

        if ($this->paid_amount <= 0) {
            $this->status = 'pending';
        } elseif ($this->paid_amount < $this->amount) {
            $this->status = 'partial';
        } else {
            $this->status = 'completed';
        }

        if ($paymentMethod) $this->payment_method = $paymentMethod;
        if ($transactionId) $this->transaction_id = $transactionId;
        if ($notes) $this->notes = $notes;

        // Auto-set reminders for partial payments
        if ($this->status === 'partial' && !$this->next_payment_date) {
            $this->next_payment_date = Carbon::now()->addDays(7);
            $this->reminder_date = Carbon::now()->addDays(5);
        }

        $this->save();
    }

    public function isReminderDue(): bool
    {
        return $this->reminder_date && Carbon::now()->gte($this->reminder_date) && $this->status === 'partial';
    }
}