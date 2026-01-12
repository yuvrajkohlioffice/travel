<?php

namespace App\Mail;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $remaining;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, $remaining)
    {
        $this->payment = $payment;
        $this->remaining = $remaining;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Payment Received - Invoice #' . $this->payment->invoice->invoice_no)
                    ->view('emails.payment_received');
    }
}