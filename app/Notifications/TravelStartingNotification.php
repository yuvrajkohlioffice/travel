<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invoice;
use App\Models\User;
use App\Channels\WhatsAppChannel; // Don't forget to import this

class TravelStartingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;
    public $senderUser; // We store the agent here to access their API Key in the Channel

    /**
     * Create a new notification instance.
     *
     * @param Invoice $invoice
     * @param User|null $senderUser
     */
    public function __construct(Invoice $invoice, ?User $senderUser = null)
    {
        $this->invoice = $invoice;
        $this->senderUser = $senderUser;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', WhatsAppChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Bon Voyage! Your Trip Starts Today - ' . $this->invoice->package_name)
            ->greeting('Hello ' . $this->invoice->primary_full_name . ',')
            ->line('We are excited to inform you that your trip for **' . $this->invoice->package_name . '** starts today!')
            ->line('**Travel Details:**')
            ->line('📅 Start Date: ' . $this->invoice->travel_start_date)
            ->line('👥 Travelers: ' . $this->invoice->total_travelers)
            ->action('View Itinerary', url('/invoices/' . $this->invoice->id))
            ->line('Have a safe and wonderful journey!');

        // If specific agent is sending, set Reply-To
        if ($this->senderUser && $this->senderUser->email) {
            $mail->replyTo($this->senderUser->email, $this->senderUser->name);
        }

        return $mail;
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsapp($notifiable)
    {
        // Formatting the WhatsApp Message
        $msg  = "🌟 *Bon Voyage {$this->invoice->primary_full_name}!*\n\n";
        $msg .= "Your trip to *{$this->invoice->package_name}* starts today! ✈️\n\n";
        $msg .= "📅 Date: {$this->invoice->travel_start_date}\n";
        $msg .= "👥 Travelers: {$this->invoice->total_travelers}\n\n";
        $msg .= "Have a safe and wonderful journey!";

        return $msg;
    }
}