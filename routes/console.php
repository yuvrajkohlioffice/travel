<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Followup;
use App\Models\User;
use Carbon\Carbon;
// IMPORT THE NOTIFICATION CLASS
use App\Notifications\FollowupReminderNotification; 

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- UPDATED FOLLOW-UP COMMAND START ---
Artisan::command('followups:remind', function () {
    // 1. Set Date
    $today = Carbon::today('Asia/Kolkata');
    $this->info("📅 Checking followups for date: " . $today->format('Y-m-d'));

    // 2. Fetch Followups
    $followups = Followup::whereDate('next_followup_date', $today)
        ->with('user', 'lead')
        ->get();

    $count = $followups->count();
    $this->info("🔍 Found {$count} followups scheduled for today.");

    if ($count === 0) {
        $this->error("❌ No followups found.");
        return;
    }

    // 3. Fetch Admins
    $superAdmins = User::where('role_id', 1)->get();
    $this->info("👤 Found {$superAdmins->count()} Super Admins to notify.");

    // 4. Loop and Send
    foreach ($followups as $followup) {
        $this->line("------------------------------------------------");
        $this->info("Processing Followup ID: {$followup->id}");

        // --- A. SEND TO ADMINS ---
        foreach ($superAdmins as $admin) {
            try {
                // CHANGED: Use notify() instead of Mail::to()
                // This triggers 'via' => ['mail', 'database']
                $admin->notify(new FollowupReminderNotification($followup));
                $this->info("   ✅ Notified Admin (DB+Mail): {$admin->email}");
            } catch (\Exception $e) {
                $this->error("   ❌ Failed Admin {$admin->email}: " . $e->getMessage());
            }
        }

        // --- B. SEND TO AGENT (USER) ---
        if ($followup->user) {
            // Prevent duplicate if Agent is also an Admin
            if (!$superAdmins->contains('id', $followup->user->id)) {
                try {
                    // CHANGED: Use notify() here too
                    $followup->user->notify(new FollowupReminderNotification($followup));
                    $this->info("   ✅ Notified Agent (DB+Mail): {$followup->user->email}");
                } catch (\Exception $e) {
                    $this->error("   ❌ Failed Agent {$followup->user->email}: " . $e->getMessage());
                }
            } else {
                $this->info("   ℹ️ Agent is also Admin (Skipping duplicate).");
            }
        } else {
            $this->warn("   ⚠️ No User assigned to this followup.");
        }
    }

    $this->info("\n✅ Process Completed.");

})->describe('Send follow-up reminders (Mail + DB) for today');
// --- UPDATED FOLLOW-UP COMMAND END ---

/*
|--------------------------------------------------------------------------
| Scheduler
|--------------------------------------------------------------------------
*/

Schedule::command('followups:remind')
    ->dailyAt('10:30')
    ->timezone('Asia/Kolkata');

Schedule::command('travel:send-reminders')
    ->dailyAt('10:30')
    ->timezone('Asia/Kolkata');