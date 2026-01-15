<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Followup;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\FollowupReminderNotification; // <--- Import the new class

class SendFollowupReminders extends Command
{
    protected $signature = 'followups:remind';
    protected $description = 'Send follow-up reminders for today';

    public function handle()
    {
        $today = Carbon::today('Asia/Kolkata');
        $this->info("📅 Checking followups for: " . $today->format('Y-m-d'));

        $followups = Followup::whereDate('next_followup_date', $today)
                             ->with('user', 'lead')
                             ->get();

        $superAdmins = User::where('role_id', 1)->get();

        foreach ($followups as $followup) {
            
            // 1. Notify Super Admins
            foreach ($superAdmins as $admin) {
                // This triggers 'via' => ['mail', 'database']
                $admin->notify(new FollowupReminderNotification($followup));
                $this->info("   -> Notified Admin: {$admin->email}");
            }

            // 2. Notify Agent (User)
            if ($followup->user) {
                // Prevent duplicate if Agent is also an Admin
                if (!$superAdmins->contains('id', $followup->user->id)) {
                    $followup->user->notify(new FollowupReminderNotification($followup));
                    $this->info("   -> Notified Agent: {$followup->user->email}");
                }
            }
        }

        $this->info('✅ All notifications sent and saved to database.');
    }
}