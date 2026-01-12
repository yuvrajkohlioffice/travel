<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;

class GuestAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct(Lead $lead, $url)
    {
        $this->lead = $lead;
        $this->url = $url;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Action Required: Your Travel Details & Invoice Portal')
                    ->view('emails.guest_access');
    }
}