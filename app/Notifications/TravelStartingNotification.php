<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invoice;

class TravelStartingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoice;
    protected $lead;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        // Assuming relationship: Invoice belongsTo Lead
        $this->lead = $invoice->lead; 
    }

    public function via($notifiable)
    {
        // specific custom channel or simple database/mail
        return ['mail', 'whatsapp']; // 'whatsapp' requires a custom driver or logic below
    }

    /**
     * 1. THE EMAIL MESSAGE
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bon Voyage! Your Trip Starts Today - ' . $this->invoice->package_name)
            ->greeting('Hello ' . $this->invoice->primary_full_name . ',')
            ->line('We are excited to inform you that your trip for **' . $this->invoice->package_name . '** starts today!')
            ->line('**Travel Details:**')
            ->line('📅 Start Date: ' . $this->invoice->travel_start_date)
            ->line('👥 Travelers: ' . $this->invoice->total_travelers)
            ->line('📍 Invoice ID: #' . $this->invoice->invoice_no)
            ->action('View Itinerary', url('/invoices/' . $this->invoice->id))
            ->line('Have a safe and wonderful journey!');
    }

    /**
     * 2. THE WHATSAPP MESSAGE
     * Note: Laravel does not have a native WhatsApp driver. 
     * You typically use Twilio, Vonage, or a custom channel.
     * This is a "Pseudo-code" implementation for structure.
     */
    public function toWhatsapp($notifiable)
    {
        // Customize this text based on your WhatsApp Provider's API requirements
        $message = "🌟 *Bon Voyage {$this->invoice->primary_full_name}!* \n\n";
        $message .= "Your trip to *{$this->invoice->package_name}* starts today! \n";
        $message .= "📅 Date: {$this->invoice->travel_start_date}\n";
        $message .= "📞 Need help? Contact us.";

        // Example: If using Twilio, you would return a TwilioMessage here.
        // For now, we return the raw data for your custom handler.
        return $message;
    }
}