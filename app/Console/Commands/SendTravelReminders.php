<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Notifications\TravelStartingNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendTravelReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'travel:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Check for travel_start_date matching today and send Email/WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $this->info("Checking for trips starting on: $today");

        // Use chunking to handle large datasets efficiently
        Invoice::with('lead') // Eager load the lead relationship
            ->whereDate('travel_start_date', $today)
            ->chunk(100, function ($invoices) {
                
                foreach ($invoices as $invoice) {
                    try {
                        $this->processInvoice($invoice);
                    } catch (\Exception $e) {
                        Log::error("Failed to send reminder for Invoice ID {$invoice->id}: " . $e->getMessage());
                    }
                }
            });

        $this->info('All reminders processed.');
    }

    protected function processInvoice($invoice)
    {
        // 1. Validate Data
        if (empty($invoice->primary_email) && empty($invoice->primary_phone)) {
            Log::warning("Skipping Invoice {$invoice->id}: No contact info found.");
            return;
        }

        // 2. Notify
        // We use the Notification facade or the notify method on the user/invoice model.
        // Since Invoice is not usually "Notifiable", we can send it "On Demand" or via the User/Lead.
        
        // Option A: If Invoice belongs to a User
        // $invoice->user->notify(new TravelStartingNotification($invoice));

        // Option B: On-Demand Notification (Send directly to the email/phone in the invoice)
        $route = \Illuminate\Support\Facades\Notification::route('mail', $invoice->primary_email);
        
        if ($invoice->primary_phone) {
            // Add WhatsApp routing here (depends on your provider)
            // $route->route('whatsapp', $invoice->primary_phone);
        }

        $route->notify(new TravelStartingNotification($invoice));

        $this->info("Sent notification for Invoice: {$invoice->invoice_no}");
    }
}