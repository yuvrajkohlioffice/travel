<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Followup;
use App\Models\User;
use App\Mail\FollowupReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // if (!app()->runningInConsole() && !session()->has('followup_reminder_checked')) {
        //     // Only run on web requests and once per session
        //     session(['followup_reminder_checked' => true]);

        //     $today = Carbon::today('Asia/Kolkata');

        //     // Fetch follow-ups for today that haven't been reminded yet
        //     $followups = Followup::whereDate('next_followup_date', $today)
        //         ->where(function($q) {
        //             $q->whereNull('reminder_sent')->orWhere('reminder_sent', false);
        //         })
        //         ->with('user', 'lead')
        //         ->get();

        //     $superAdmins = User::where('role_id', 1)->get();

        //     foreach ($followups as $followup) {

        //         // Queue email to super admins
        //         foreach ($superAdmins as $admin) {
        //             if ($admin->email) {
        //                 Mail::to($admin->email)->queue(new FollowupReminderMail($followup));
        //             }
        //         }

        //         // Queue email to the user who created the follow-up
        //         if ($followup->user && $followup->user->email) {
        //             Mail::to($followup->user->email)->queue(new FollowupReminderMail($followup));
        //         }

        //         // Mark follow-up as reminded
        //         $followup->reminder_sent = true;
        //         $followup->save();
        //     }
        // }
    }
}
