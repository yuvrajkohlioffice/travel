<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Models\Followup;
use App\Models\User;
use App\Mail\FollowupReminderMail;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Artisan::command('followups:remind', function () {
    $today = Carbon::today('Asia/Kolkata');

    $followups = Followup::whereDate('next_followup_date', $today)
        ->with('user', 'lead')
        ->get();

    $superAdmins = User::where('role_id', 1)->get();

    foreach ($followups as $followup) {

        // Send to super admins
        foreach ($superAdmins as $admin) {
            if ($admin->email) {
                Mail::to($admin->email)->send(new FollowupReminderMail($followup));
            }
        }

        // Send to user who created the follow-up
        if ($followup->user && $followup->user->email) {
            Mail::to($followup->user->email)->send(new FollowupReminderMail($followup));
        }

        $this->info('Reminder sent for followup ID: ' . $followup->id);
    }

    $this->info('All follow-up reminders sent successfully.');
})->describe('Send follow-up reminders for today');
