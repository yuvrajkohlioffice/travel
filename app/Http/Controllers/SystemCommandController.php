<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Exception;

class SystemCommandController extends Controller
{
    /* ===============================
       CLEAR ALL CACHE
    =============================== */
    public function clearAll()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return redirect()->back()->with('success', 'All caches cleared successfully!');
        } catch (Exception $e) {
            Log::error('Clear cache failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to clear cache!');
        }
    }

    /* ===============================
       OPTIMIZE APP
    =============================== */
    public function optimizeApp()
    {
        try {
            Artisan::call('optimize');

            return redirect()->back()->with('success', 'Application optimized successfully!');
        } catch (Exception $e) {
            Log::error('Optimize failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to optimize application!');
        }
    }

    /* ===============================
       CLEAR + OPTIMIZE (RECOMMENDED)
    =============================== */
    public function clearAndOptimize()
    {
        try {
            Artisan::call('optimize:clear');
            Artisan::call('optimize');

            return redirect()->back()->with('success', 'Application cleared and optimized successfully!');
        } catch (Exception $e) {
            Log::error('Clear & optimize failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Operation failed!');
        }
    }

    /* ===============================
       STORAGE LINK
    =============================== */
    public function storageLink()
    {
        try {
            Artisan::call('storage:link');

            return redirect()->back()->with('success', 'Storage link created successfully!');
        } catch (Exception $e) {
            Log::error('Storage link failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to create storage link!');
        }
    }

    /* ===============================
       MIGRATE
    =============================== */
    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return redirect()->back()->with('success', 'Migration executed successfully!');
        } catch (Exception $e) {
            Log::error('Migration failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Migration failed!');
        }
    }

    /* ===============================
       DEPLOY
    =============================== */
    public function deploy()
{
    try {
        // Prevent accidental production deploy
        if (app()->isProduction()) {
            return redirect()->back()->with('error', 'Deploy is disabled in production for safety!');
        }

        // 1️⃣ Clear caches
        $output = [];
        try {
            Artisan::call('optimize:clear');
            $output['clear_cache'] = Artisan::output();
        } catch (\Exception $e) {
            Log::error('Clear cache failed during deploy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to clear cache!');
        }

        // 2️⃣ Run migrations
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output['migrate'] = Artisan::output();
        } catch (\Exception $e) {
            Log::error('Migration failed during deploy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Migration failed!');
        }

        // 3️⃣ Optimize application
        try {
            Artisan::call('optimize');
            $output['optimize'] = Artisan::output();
        } catch (\Exception $e) {
            Log::error('Optimize failed during deploy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Application optimization failed!');
        }

        return redirect()->back()->with('success', 'Deploy executed successfully!');

    } catch (\Exception $e) {
        Log::error('Deploy failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Deploy failed!');
    }
}
public function runDailyReminders()
{
    try {
        $outputMessages = [];

        // 1. Run Follow-up Reminders
        Artisan::call('followups:remind');
        $outputMessages[] = "<strong>Follow-ups:</strong><br>" . nl2br(Artisan::output());

        // 2. Run Travel Reminders
        Artisan::call('travel:send-reminders');
        $outputMessages[] = "<strong>Travel Reminders:</strong><br>" . nl2br(Artisan::output());

        // Combine outputs
        $finalOutput = implode('<br><hr><br>', $outputMessages);

        return redirect()->back()->with('success', 'Daily commands executed!<br><br>' . $finalOutput);

    } catch (\Exception $e) {
        \Log::error('Manual command run failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to run commands: ' . $e->getMessage());
    }
}


}
