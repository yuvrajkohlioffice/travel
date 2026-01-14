<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;
use App\Models\Followup;
use App\Models\User;
use App\Mail\FollowupReminderMail;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// 1. Inspire Command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 2. Follow-up Reminders Command Definition
// 2. Follow-up Reminders Command Definition
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
        $this->error("❌ No followups found in DB for this date. Check your database 'next_followup_date' column.");
        return;
    }

    // 3. Fetch Admins
    $superAdmins = User::where('role_id', 1)->get();
    $this->info("👤 Found {$superAdmins->count()} Super Admins to notify.");

    // 4. Loop and Send
    foreach ($followups as $followup) {
        $this->line("------------------------------------------------");
        $this->info("Processing Followup ID: {$followup->id}");

        // --- SEND TO ADMINS ---
        foreach ($superAdmins as $admin) {
            if ($admin->email) {
                try {
                    Mail::to($admin->email)->send(new FollowupReminderMail($followup));
                    $this->info("   ✅ Sent to Admin: {$admin->email}");
                } catch (\Exception $e) {
                    $this->error("   ❌ Failed sending to Admin ({$admin->email}): " . $e->getMessage());
                }
            } else {
                $this->warn("   ⚠️ Admin (ID: {$admin->id}) has no email.");
            }
        }

        // --- SEND TO AGENT (USER) ---
        // Check if user exists
        if (!$followup->user) {
            $this->error("   ⚠️ Skipping Agent: No User assigned to this followup (user_id is null or invalid).");
            continue;
        }

        if ($followup->user->email) {
            try {
                Mail::to($followup->user->email)->send(new FollowupReminderMail($followup));
                $this->info("   ✅ Sent to Agent: {$followup->user->email}");
            } catch (\Exception $e) {
                $this->error("   ❌ Failed sending to Agent ({$followup->user->email}): " . $e->getMessage());
            }
        } else {
            $this->warn("   ⚠️ Agent (ID: {$followup->user->id}) has no email address.");
        }
    }

    $this->info("\n✅ Process Completed.");

})->describe('Send follow-up reminders for today');
/*
|--------------------------------------------------------------------------
| Scheduler
|--------------------------------------------------------------------------
|
| Here we schedule both the Closure command defined above AND the 
| class-based command defined separately.
|
*/

// Schedule Follow-ups for 10:00 AM
Schedule::command('followups:remind')
    ->dailyAt('10:15')
    ->timezone('Asia/Kolkata');

// Schedule Travel Reminders for 10:00 AM
Schedule::command('travel:send-reminders')
    ->dailyAt('10:15')
    ->timezone('Asia/Kolkata');