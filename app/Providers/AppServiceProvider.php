<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Followup;
use App\Models\User;
use App\Mail\FollowupReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('unreadNotifications', Auth::user()->unreadNotifications);
            }
        });
    }
}
