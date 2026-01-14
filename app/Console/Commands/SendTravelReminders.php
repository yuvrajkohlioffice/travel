<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\User; // Import User
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

        // Load Invoice AND the related User (Agent)
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
        // ---------------------------------------------------------
        // 1. Identify the Agent (Sender) for API Keys
        // ---------------------------------------------------------
        // Priority: 1. User assigned to Invoice -> 2. User assigned to Lead -> 3. Null (System)
        $agent = $invoice->user;

        if (!$agent && $invoice->lead) {
            // If invoice has no user, check the Lead's user
            $agent = $invoice->lead->user;
        }

        // ---------------------------------------------------------
        // 2. Identify Recipient Contact Info
        // ---------------------------------------------------------
        $email = $invoice->lead->email;
        $phone = $invoice->phone_number;
        if (empty($phone) && $invoice->lead) {
            $code = $invoice->lead->phone_code ?? ''; // e.g. "91" or "+91"
            $number = $invoice->lead->phone_number ?? ''; // e.g. "9876543210"

            if (!empty($number)) {
                // Remove '+' from code if present, just to be safe for API
                $cleanCode = str_replace('+', '', $code);
                $phone = $cleanCode . $number;
            }
        }

        // If neither exists, we skip this invoice entirely
        if (empty($email) && empty($phone)) {
            $this->warn("⚠️  Skipping Invoice #{$invoice->invoice_no}: No contact info found.");
            return;
        }

        // ---------------------------------------------------------
        // 3. Build the Notification Route (Email / WhatsApp / Both)
        // ---------------------------------------------------------
        $notifiable = null;

        if ($email && $phone) {
            // Case A: BOTH Email and Phone exist
            $notifiable = Notification::route('mail', $email)->route(WhatsAppChannel::class, $phone);
        } elseif ($email) {
            // Case B: ONLY Email exists
            $notifiable = Notification::route('mail', $email);
        } elseif ($phone) {
            // Case C: ONLY Phone exists (Email is empty/null)
            $notifiable = Notification::route(WhatsAppChannel::class, $phone);
        }

        // ---------------------------------------------------------
        // 4. Send the Notification
        // ---------------------------------------------------------
        $this->line('------------------------------------------');
        $this->info("Invoice #{$invoice->invoice_no} | Client: {$invoice->primary_full_name}");

        // Log who is sending it (for debugging)
        if ($agent) {
            $this->info("   👤 Sender (Agent): {$agent->name} (ID: {$agent->id})");
        } else {
            $this->warn('   🤖 Sender: System (No specific agent found)');
        }

        try {
            // We pass the $agent to the Notification so it knows which WhatsApp API Key to use
            $notifiable->notify(new TravelStartingNotification($invoice, $agent));

            if ($email) {
                $this->info("   ✅ Email queued for: $email");
            }
            if ($phone) {
                $this->info("   ✅ WhatsApp queued for: $phone");
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Failed to send: ' . $e->getMessage());
            Log::error("Travel Reminder Failed (Invoice ID: {$invoice->id}): " . $e->getMessage());
        }
    }
}
