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

    // 1. Identify Agent
    $agent = $invoice->user ?? ($invoice->lead->user ?? null);

    // 2. Resolve Contact Info
    $clientEmail = $invoice->primary_email ?: ($invoice->lead->email ?? null);
    $clientPhone = $invoice->primary_phone;
    if (empty($clientPhone) && $invoice->lead) {
        $code = preg_replace('/[^0-9]/', '', $invoice->lead->phone_code ?? '');
        $number = preg_replace('/[^0-9]/', '', $invoice->lead->phone_number ?? '');
        $clientPhone = !empty($number) ? $code . $number : null;
    }

    if (!$clientPhone && !$clientEmail) {
        $this->warn('   ⚠️  Skipping: No Email OR Phone found.');
        return;
    }

    $results = [];

    // --- STEP 1: TRY WHATSAPP FIRST ---
    if ($clientPhone) {
        try {
            Notification::route(WhatsAppChannel::class, $clientPhone)
                ->notify(new TravelStartingNotification($invoice, $agent));
            $this->info("   ✅ WhatsApp Sent: $clientPhone");
            $results[] = 'WhatsApp';
        } catch (\Exception $e) {
            $this->error("   ❌ WhatsApp Failed: " . $e->getMessage());
            Log::error("WhatsApp Failed for Invoice #{$invoice->id}: " . $e->getMessage());
        }
    }

    // --- STEP 2: TRY EMAIL SECOND ---
    if ($clientEmail) {
        try {
            Notification::route('mail', $clientEmail)
                ->notify(new TravelStartingNotification($invoice, $agent));
            $this->info("   ✅ Email Sent: $clientEmail");
            $results[] = 'Email';
        } catch (\Exception $e) {
            $this->error("   ❌ Email Failed: " . $e->getMessage());
            Log::error("Email Failed for Invoice #{$invoice->id}: " . $e->getMessage());
        }
    }
}
}
