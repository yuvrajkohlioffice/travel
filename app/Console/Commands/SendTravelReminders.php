<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\User; 
use App\Notifications\TravelStartingNotification;
use App\Channels\WhatsAppChannel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendTravelReminders extends Command
{
    protected $signature = 'travel:send-reminders';
    protected $description = 'Send reminders with correct Agent/User API keys';

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
        // 1. Identify the Agent (Sender) - This is also the user we want to notify internally
        $agent = $invoice->user;

        if (!$agent && $invoice->lead) {
            $agent = $invoice->lead->user;
        }

        // 2. Identify Client Recipient Contact Info
        $email = $invoice->lead->email;
        $phone = $invoice->phone_number;
        if (empty($phone) && $invoice->lead) {
            $code = $invoice->lead->phone_code ?? '';
            $number = $invoice->lead->phone_number ?? '';
            if (!empty($number)) {
                $cleanCode = str_replace('+', '', $code);
                $phone = $cleanCode . $number;
            }
        }

        // ---------------------------------------------------------
        // 3. Send to CLIENT (Email + WhatsApp)
        // ---------------------------------------------------------
        $this->line('------------------------------------------');
        $this->info("Invoice #{$invoice->invoice_no} | Client: {$invoice->primary_full_name}");

        // Build Client Route
        $clientNotifiable = null;
        if ($email && $phone) {
            $clientNotifiable = Notification::route('mail', $email)->route(WhatsAppChannel::class, $phone);
        } elseif ($email) {
            $clientNotifiable = Notification::route('mail', $email);
        } elseif ($phone) {
            $clientNotifiable = Notification::route(WhatsAppChannel::class, $phone);
        }

        if ($clientNotifiable) {
            try {
                // Send to Client
                $clientNotifiable->notify(new TravelStartingNotification($invoice, $agent));
                $this->info("   ✅ Sent to Client (Email/WhatsApp)");
            } catch (\Exception $e) {
                $this->error('   ❌ Failed to send to Client: ' . $e->getMessage());
                Log::error("Travel Reminder Client Failed: " . $e->getMessage());
            }
        } else {
            $this->warn("   ⚠️  Skipping Client: No contact info found.");
        }

        // ---------------------------------------------------------
        // 4. Send to AGENT / USER (Email + In-App Database)
        // ---------------------------------------------------------
        if ($agent) {
            try {
                // Laravel Users use the 'Notifiable' trait by default.
                // We pass the same notification instance. 
                // IMPORTANT: Your Notification class must handle the logic to switch channels (see Part 2 below).
                $agent->notify(new TravelStartingNotification($invoice, $agent));
                
                $this->info("   ✅ Sent to Agent: {$agent->name} (Email: {$agent->email} + In-App)");
            } catch (\Exception $e) {
                $this->error('   ❌ Failed to send to Agent: ' . $e->getMessage());
                Log::error("Travel Reminder Agent Failed: " . $e->getMessage());
            }
        } else {
            $this->warn('   ⚠️  No Agent/User assigned to receive internal notification.');
        }
    }
}