<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Invoice;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\User;

class TravelStartingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;
    public $agent;

    public function __construct($invoice, $agent)
    {
        $this->invoice = $invoice;
        $this->agent = $agent;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        // Send via Email AND save to Database (for the dashboard)
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $leadName = $this->followup->lead->name ?? 'Unknown Lead';
        $agentName = $this->followup->user->name ?? 'Unknown Agent';

        return (new MailMessage)
                    ->subject("📅 Follow-up Reminder: $leadName")
                    ->greeting("Hello " . $notifiable->name . ",")
                    ->line("This is a reminder for a scheduled follow-up today.")
                    ->line("**Lead Name:** " . $leadName)
                    ->line("**Assigned Agent:** " . $agentName)
                    ->line("**Follow-up Remarks:** " . $this->followup->remarks)
                    ->action('View Follow-up', url('/admin/leads/' . $this->followup->lead_id))
                    ->line('Please ensure this is actioned today.');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsapp($notifiable)
    {
        // Construct a friendly message
        $message = "Hello {$this->invoice->primary_full_name}, your trip starts today! 🌍✈️\n\n";
        $message .= "Invoice: #{$this->invoice->invoice_no}\n";
        $message .= "Have a wonderful journey!";
        $message .=  "\n\n";

        // Optional: Link to the PDF or Invoice View
        $message .=  url('/portal/invoice/' . $this->invoice->id);

        // Optional: Link to the PDF or Invoice View
        // $url = url('/storage/invoices/' . $this->invoice->invoice_no . '.pdf');
        
        return [
            'message' => $message,
            // 'url' => $url // Uncomment if you have a valid file URL
        ];
    }

    /**
     * Database notification structure
     */
    public function toArray($notifiable)
    {
        return [
            'followup_id' => $this->followup->id,
            'lead_id'     => $this->followup->lead_id,
            'title'       => 'Follow-up Due Today',
            'message'     => 'Lead: ' . ($this->followup->lead->name ?? 'N/A'),
            'link'        => url('/admin/leads/' . $this->followup->lead_id),
        ];
    }
}