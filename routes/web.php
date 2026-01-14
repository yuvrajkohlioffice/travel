<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Controllers
use App\Http\Controllers\RoleRouteController;
use App\Http\Controllers\{CompanyController, SystemCommandController, FollowupReportController, PaymentMethodController, PickupPointController, FollowupReasonController, LeadStatusController, MessageTemplateController, CarController, DashboardController, UserController, InvoiceController, PackageTypeController, PackageCategoryController, DifficultyTypeController, HotelController, RoleController, PackageController, LeadController, FollowupController, PaymentController, WhatsAppController};
use Livewire\Volt\Volt;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Artisan;


// Guest / Client Portal Routes
Route::prefix('portal')->group(function () {
    // 1. Show Login Page or Redirect to Form if logged in
    Route::get('/login/{lead_id}', [App\Http\Controllers\GuestInvoiceController::class, 'showLogin'])->name('guest.login');

    // 2. Verify Password
    Route::post('/verify', [App\Http\Controllers\GuestInvoiceController::class, 'verifyPassword'])->name('guest.verify');

    // 3. The Main Form (Protected by Session)
    Route::get('/form/{lead_id}', [App\Http\Controllers\GuestInvoiceController::class, 'showForm'])->name('guest.form');

    // 4. Update Details (Save)
    Route::post('/update/{lead_id}', [App\Http\Controllers\GuestInvoiceController::class, 'updateDetails'])->name('guest.update');
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| User Settings (Volt)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Route::resource('templates', MessageTemplateController::class);

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(when(Features::canManageTwoFactorAuthentication() && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'), ['password.confirm'], []))
        ->name('two-factor.show');
});

/*
|--------------------------------------------------------------------------
| Open API routes (No Auth)
|--------------------------------------------------------------------------
*/
Route::get('/cars', fn() => \App\Models\Car::all());
Route::get('/hotels', fn() => \App\Models\Hotel::all());

Route::post('/invoices/create-quick', [InvoiceController::class, 'createQuickInvoice']);
Route::get('/api/cars', [\App\Http\Controllers\Api\CarController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Authenticated Application Routes
|--------------------------------------------------------------------------
*/

Route::get('/packages/partial-item-row', [PackageController::class, 'partialItemRow'])->name('packages.partial-item-row');
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', \App\Http\Middleware\CheckRoleRoute::class])->group(function () {
    /*
        |--------------------------------------------------------------------------
        | Lead Management
        |--------------------------------------------------------------------------
        */
        Route::get('/test-whatsapp', [WhatsAppController::class, 'testConnection']);
        Route::get('/system/run-daily-reminders', [SystemCommandController::class, 'runDailyReminders'])
    ->name('system.run.daily');
    Route::post('/leads/send-access/{lead_id}', [App\Http\Controllers\GuestInvoiceController::class, 'sendAccessLink'])->name('leads.send_access');
    Route::prefix('system')
        ->name('system.')
        ->group(function () {
            Route::get('/deploy', [SystemCommandController::class, 'deploy'])->name('deploy');

            Route::get('/optimize', [SystemCommandController::class, 'clearAndOptimize'])->name('optimize');

            Route::get('/storage-link', [SystemCommandController::class, 'storageLink'])->name('storage.link');
        });
    Route::get('/composer-dump', function () {
        // 🔒 Only allow admin
        abort_unless(auth()->user()->role_id === 1, 403);

        // 🔹 Replace this with the path you got from `which composer`
        $composerPath = '/usr/bin/composer';

        $process = new Process([$composerPath, 'dump-autoload']);
        $process->setWorkingDirectory(base_path()); // Laravel root
        $process->setTimeout(300); // 5 minutes

        $process->run();

        if (!$process->isSuccessful()) {
            // show detailed error output for debugging
            $errorOutput = $process->getErrorOutput();
            return back()->with('error', "Composer failed: \n$errorOutput");
        }

        return back()->with('success', "✅ Composer dump-autoload executed successfully! \n" . $process->getOutput());
    })->name('composer.dump');
    
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');

    Route::get('/leads/data', [LeadController::class, 'getLeadsData'])->name('leads.data');
    Route::get('/leads/counts', [LeadController::class, 'getLeadsCounts'])->name('leads.counts');

    Route::post('/followup/store', [FollowupController::class, 'store'])->name('followup.store');
    Route::get('/leads/{lead}/details', [FollowupController::class, 'getLeadDetails'])->name('leads.details');

    Route::get('/leads/{lead}/assign', [LeadController::class, 'assignForm'])->name('leads.assign.form');
    Route::post('/leads/{lead}/assign', [LeadController::class, 'assignStore'])->name('leads.assign.store');
    Route::delete('/leads/assignment/{id}/delete', [LeadController::class, 'deleteAssignment'])->name('leads.assign.delete');

    Route::post('/leads/import', [LeadController::class, 'importLeads'])->name('leads.import');
    Route::get('/leads/{lead}/json', [LeadController::class, 'showJson'])->name('lead.json.show');
    Route::post('/leads/bulk-assign', [LeadController::class, 'bulkAssign'])->name('leads.bulkAssign');

    Route::resource('leads', LeadController::class);

    Route::resource('companies', CompanyController::class);

    /*
        |--------------------------------------------------------------------------
        | WhatsApp Messaging
        |--------------------------------------------------------------------------
        */
    Route::prefix('whatsapp')
        ->name('whatsapp.')
        ->group(function () {
            Route::post('/send-text', [WhatsAppController::class, 'sendText'])->name('send-text');

            Route::post('/send-media', [WhatsAppController::class, 'sendMedia'])->name('send-media');

            Route::post('/send-media-json', [WhatsAppController::class, 'sendMediaJson'])->name('send-media-json');
        });

    /*
        |--------------------------------------------------------------------------
        | Payment Management
        |--------------------------------------------------------------------------
        */
    Route::resource('role_routes', RoleRouteController::class);

    Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);
    Route::get('/followup-report', [FollowupReportController::class, 'index'])->name('followup.report');
    Route::get('followup-report/leads', [FollowupReportController::class, 'getLeads'])->name('followup_report.leads');
    Route::get('/followup-report/data', [FollowupReportController::class, 'getReport'])->name('followup.report.data');
    Route::post('/payment-methods/restore/{id}', [PaymentMethodController::class, 'restore'])->name('payment-methods.restore');
    Route::delete('/payment-methods/force-delete/{id}', [PaymentMethodController::class, 'forceDelete'])->name('payment-methods.forceDelete');
    Route::prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::get('/', [PaymentController::class, 'index'])->name('index'); // Page + DataTable AJAX
            Route::post('/', [PaymentController::class, 'store'])->name('store');
            Route::put('/{id}', [PaymentController::class, 'update'])->name('update');
            Route::delete('/{id}', [PaymentController::class, 'destroy'])->name('destroy');

            Route::get('/reminders', [PaymentController::class, 'reminders'])->name('reminders');
        });

    /*
        |--------------------------------------------------------------------------
        | Invoice
        |--------------------------------------------------------------------------
        */
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::delete('/invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    /*
        |--------------------------------------------------------------------------
        | Packages
        |--------------------------------------------------------------------------
        */
    Route::patch('packages/{id}/restore', [PackageController::class, 'restore'])->name('packages.restore');
    Route::delete('packages/item/{item}', [PackageController::class, 'deleteRelation'])->name('packages.item.delete');

    Route::put('packages/item/{item}', [PackageController::class, 'updatePackageItem'])->name('packages.item.update');
    Route::get('/payment-methods/active', [PaymentMethodController::class, 'active'])->name('payment-methods.active');

    Route::resource('packages', PackageController::class);

    Route::prefix('packages')->group(function () {
        Route::get('{package}', [PackageController::class, 'show'])->name('packages.show');
        Route::get('{package}/edit-relations', [PackageController::class, 'editRelations'])->name('packages.edit-relations');
        Route::post('{package}/update-relations', [PackageController::class, 'updateRelations'])->name('packages.update-relations');

        Route::get('/{package}/json', [PackageController::class, 'apiShow'])->name('packages.json');
    });

    Route::post('/leads/send-package-email', [PackageController::class, 'sendPackageEmail'])->name('leads.sendPackageEmail');

    /*
        |--------------------------------------------------------------------------
        | System Resources
        |--------------------------------------------------------------------------
        */
    Route::resources([
        'users' => UserController::class,
        'cars' => CarController::class,
        'hotels' => HotelController::class,
        'package-types' => PackageTypeController::class,
        'package-categories' => PackageCategoryController::class,
        'difficulty-types' => DifficultyTypeController::class,
        'roles' => RoleController::class,
        'pickup-points' => PickupPointController::class,
    ]);

    /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
    // routes/api.php
    Route::get('/followup-reasons-api', [FollowupReasonController::class, 'indexApi'])->name('followup-reasons.indexApi');

    Route::resource('followup-reasons', FollowupReasonController::class)->except(['create', 'show']);
    Route::resource('lead-statuses', LeadStatusController::class)->except(['create', 'show']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
