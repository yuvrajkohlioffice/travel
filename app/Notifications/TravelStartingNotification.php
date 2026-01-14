<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\User; // Import User model

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
        // 1. If the recipient is a USER (The Agent), send Email and In-App (Database)
        if ($notifiable instanceof User) {
            return ['mail', 'database'];
        }

        // 2. If the recipient is a CLIENT (Anonymous route), send Email and WhatsApp
        // This checks if we are using Notification::route()
        return ['mail', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $subject = "Travel Reminder: Invoice #{$this->invoice->invoice_no}";
        
        // You can customize the message based on who receives it
        if ($notifiable instanceof User) {
             return (new MailMessage)
                ->subject("Reminder for Client: " . $subject)
                ->line("Your client {$this->invoice->primary_full_name} is starting their travel today.")
                ->action('View Invoice', url('/admin/invoices/' . $this->invoice->id));
        }

        // Standard Client Email
        return (new MailMessage)
                    ->subject($subject)
                    ->line('Your trip is starting soon!')
                    ->line('Please find your details attached.');
    }

    /**
     * Get the array representation of the notification (For Database/In-App).
     */
    public function toArray($notifiable)
    {
        return [
            'invoice_id' => $this->invoice->id,
            'title'      => 'Trip Starting Today',
            'message'    => "Client {$this->invoice->primary_full_name} starts travel today.",
            'link'       => url('/invoices/' . $this->invoice->id),
        ];
    }
}