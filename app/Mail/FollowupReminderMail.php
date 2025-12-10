<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Followup;

class FollowupReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $followup;

    public function __construct(Followup $followup)
    {
        $this->followup = $followup;
    }

    public function build()
    {
        return $this->subject('Follow-up Reminder')
                    ->view('emails.followup_reminder');
    }
}
