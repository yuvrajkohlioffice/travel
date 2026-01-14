<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Notifications\TravelStartingNotification;
use App\Channels\WhatsAppChannel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Notification;

class SendTravelReminders extends Command
{
    protected $signature = 'travel:send-reminders';
    protected $description = 'Send travel reminders via WhatsApp and Email';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        $this->info("📅 Checking trips for: $today");

        $invoices = Invoice::with(['user', 'lead.user'])
            ->whereDate('travel_start_date', $today)
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('✅ No trips starting today.');
            return;
        }

        foreach ($invoices as $invoice) {
            $this->processInvoice($invoice);
        }
    }

    protected function processInvoice($invoice)
    {
        $this->line('------------------------------------------');
        $this->info("Invoice #{$invoice->invoice_no} | Client: {$invoice->primary_full_name}");

        // 1. Identify the Agent (Sender) - for context inside the notification
        $agent = $invoice->user;
        if (!$agent && $invoice->lead) {
            $agent = $invoice->lead->user;
        }

        // 2. GET EMAIL (Invoice -> Lead fallback)
        $clientEmail = $invoice->primary_email;
        if (empty($clientEmail) && $invoice->lead) {
            $clientEmail = $invoice->lead->email;
        }

        // 3. GET PHONE (Invoice -> Lead fallback)
        $clientPhone = $invoice->primary_phone; // Assuming invoice has direct number

        if (empty($clientPhone) && $invoice->lead) {
            // Clean and combine Lead phone code + number
            $code = preg_replace('/[^0-9]/', '', $invoice->lead->phone_code ?? '');
            $number = preg_replace('/[^0-9]/', '', $invoice->lead->phone_number ?? '');
            
            if (!empty($number)) {
                $clientPhone = $code . $number;
            }
        }

        // 4. Construct the Recipient (Anonymous Notifiable)
        // We start an empty route chain
        $notifiable = null;

        if ($clientEmail) {
            $notifiable = Notification::route('mail', $clientEmail);
        }

        if ($clientPhone) {
            if ($notifiable) {
                // If we already have email, append WhatsApp route
                $notifiable->route(WhatsAppChannel::class, $clientPhone);
            } else {
                // If no email, start with WhatsApp route
                $notifiable = Notification::route(WhatsAppChannel::class, $clientPhone);
            }
        }

        // 5. Send Notification
        if ($notifiable) {
            try {
                $notifiable->notify(new TravelStartingNotification($invoice, $agent));
                
                $channels = [];
                if ($clientEmail) $channels[] = "Email ($clientEmail)";
                if ($clientPhone) $channels[] = "WhatsApp ($clientPhone)";
                
                $this->info("   ✅ Sent to Client via: " . implode(' & ', $channels));

            } catch (\Exception $e) {
                $this->error('   ❌ Failed to send: ' . $e->getMessage());
                Log::error("Travel Reminder Failed for Invoice #{$invoice->id}: " . $e->getMessage());
            }
        } else {
            $this->warn("   ⚠️  Skipping: No Email OR Phone found.");
        }
    }
}