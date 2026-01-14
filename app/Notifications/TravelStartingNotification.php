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
        // 1. If sending to the AGENT (System User)
        if ($notifiable instanceof User) {
            return ['mail', 'database'];
        }

        // 2. If sending to the CLIENT (Anonymous from Command)
        // This will automatically use whatever routes (mail/whatsapp) we set in the Command
        return ['mail', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $subject = "Trip Reminder: Invoice #{$this->invoice->invoice_no}";

        // Email for Agent
        if ($notifiable instanceof User) {
             return (new MailMessage)
                ->subject("Reminder: Client Trip Starts Today")
                ->line("Your client {$this->invoice->primary_full_name} is starting their travel today.")
                ->action('View Invoice', url('/admin/invoices/' . $this->invoice->id));
        }

        // Email for Client
        return (new MailMessage)
            ->subject("Bon Voyage! Your Trip Starts Today")
            ->greeting("Hello {$this->invoice->primary_full_name},")
            ->line("We are excited to remind you that your trip starts today!")
            ->line("Invoice Reference: #{$this->invoice->invoice_no}")
            ->line("Please find your travel details attached or via the link below.")
            // ->action('View Itinerary', url('/invoices/view/' . $this->invoice->id)) // Optional Link
            ->line('Have a safe and wonderful journey!');
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
            'invoice_id' => $this->invoice->id,
            'title'      => 'Trip Starting Today',
            'message'    => "Client {$this->invoice->primary_full_name} starts travel today.",
            'link'       => url('/portal/invoices/' . $this->invoice->id),
        ];
    }
}