<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Followup;

class FollowupReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $followup;

    // 1. Accept the Followup Model
    public function __construct(Followup $followup)
    {
        $this->followup = $followup;
    }

    // 2. Define Channels: 'mail' sends email, 'database' saves to table
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // 3. Email Structure
    public function toMail($notifiable)
    {
        $leadName = $this->followup->lead->name ?? 'Unknown Lead';

        return (new MailMessage)
                    ->subject("📅 Follow-up Reminder: $leadName")
                    ->greeting("Hello " . $notifiable->name . ",")
                    ->line("You have a follow-up scheduled for today.")
                    ->line("**Lead Name:** " . $leadName)
                    ->line("**Remarks:** " . $this->followup->remarks)
                    ->action('View Lead', url('/leads/' . $this->followup->lead_id));
    }

    // 4. Database Structure (This is what goes into the table)
    public function toArray($notifiable)
    {
        return [
            'followup_id' => $this->followup->id,
            'lead_id'     => $this->followup->lead_id,
            'title'       => 'Follow-up Due Today',
            'message'     => 'Lead: ' . ($this->followup->lead->name ?? 'N/A'),
            'link'        => url('/leads/' . $this->followup->lead_id),
        ];
    }
}