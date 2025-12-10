<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Followup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\FollowupReminderMail;

class SendFollowupReminders extends Command
{
    protected $signature = 'followups:remind';
    protected $description = 'Send follow-up reminders for today';

    public function handle()
    {
        $today = Carbon::today('Asia/Kolkata');

        $followups = Followup::whereDate('next_followup_date', $today)->with('user', 'lead')->get();

        $superAdmins = User::where('role_id', 1)->get(); // Assuming role_id=1 is super admin

        foreach ($followups as $followup) {

            // Send to super admins
            foreach ($superAdmins as $admin) {
                Mail::to($admin->email)->send(new FollowupReminderMail($followup));
            }

            // Send to user who created the follow-up
            if ($followup->user && $followup->user->email) {
                Mail::to($followup->user->email)->send(new FollowupReminderMail($followup));
            }

            $this->info('Reminder sent for followup ID: ' . $followup->id);
        }

        $this->info('All follow-up reminders sent successfully.');
    }
}
