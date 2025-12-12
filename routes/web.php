<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Controllers
use App\Http\Controllers\{CompanyController, MessageTemplateController, CarController, DashboardController, UserController, InvoiceController, PackageTypeController, PackageCategoryController, DifficultyTypeController, HotelController, RoleController, PackageController, LeadController, FollowupController, PaymentController, WhatsAppController};
use Livewire\Volt\Volt;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\Artisan;

Route::get('/link-storage', function () {
    Artisan::call('storage:link');

    return back()->with('success', 'Storage linked successfully!');
});
Route::get('/deploy', function () {
    try {
        // 1. Run npm build
        $npm = new Process(['npm', 'run', 'build']);
        $npm->run();

        if (!$npm->isSuccessful()) {
            throw new ProcessFailedException($npm);
        }

        // 2. Run optimize
        Artisan::call('optimize');

        // 3. Run migrate
        Artisan::call('migrate', ['--force' => true]);

        // 4. Run storage link
        Artisan::call('storage:link');

        return back()->with('success', 'Deployment commands executed successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
})->middleware('auth');
Route::get('/optimize-app', function () {
    Artisan::call('optimize');
     return back()->with('success', 'Optimize  successfully!');
});
Route::get('/run-npm-build', function () {
    $process = new Process(['npm', 'run', 'build']);
    $process->run();

    if (!$process->isSuccessful()) {
        return back()->with('error', $process->getErrorOutput());
    }

    return back()->with('success', 'NPM Build completed!');
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
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    /*
        |--------------------------------------------------------------------------
        | Lead Management
        |--------------------------------------------------------------------------
        */
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');

    Route::get('/leads/data', [LeadController::class, 'getLeadsData'])->name('leads.data');
    Route::get('/leads/counts', [LeadController::class, 'getLeadsCounts'])->name('leads.counts');

    Route::post('/followup/store', [FollowupController::class, 'store'])->name('followup.store');
    Route::get('/leads/{lead}/details', [FollowupController::class, 'getLeadDetails']);

    Route::get('/leads/{lead}/assign', [LeadController::class, 'assignForm'])->name('leads.assign.form');
    Route::post('/leads/{lead}/assign', [LeadController::class, 'assignStore'])->name('leads.assign.store');
    Route::delete('/leads/assignment/{id}/delete', [LeadController::class, 'deleteAssignment'])->name('leads.assign.delete');

    Route::post('/leads/import', [LeadController::class, 'importLeads'])->name('leads.import');
    Route::get('/leads/{lead}/json', [LeadController::class, 'showJson']);
    Route::post('/leads/bulk-assign', [LeadController::class, 'bulkAssign'])->name('leads.bulkAssign');

    Route::resource('leads', LeadController::class);

    Route::resource('companies', CompanyController::class);

    /*
        |--------------------------------------------------------------------------
        | WhatsApp Messaging
        |--------------------------------------------------------------------------
        */
    Route::prefix('whatsapp')->group(function () {
        Route::post('/send-text', [WhatsAppController::class, 'sendText']);
        Route::post('/send-media', [WhatsAppController::class, 'sendMedia']);
        Route::post('/send-media-json', [WhatsAppController::class, 'sendMediaJson']);
    });

    /*
        |--------------------------------------------------------------------------
        | Payment Management
        |--------------------------------------------------------------------------
        */
    Route::prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index']);
        Route::post('/', [PaymentController::class, 'store']);
        Route::put('/{id}', [PaymentController::class, 'update']);
        Route::delete('/{id}', [PaymentController::class, 'destroy']);
        Route::get('/reminders', [PaymentController::class, 'reminders']);
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
    Route::delete('packages/item/{item}', [PackageController::class, 'deleteRelation'])->name('packages.item.delete');

    Route::put('packages/item/{item}', [PackageController::class, 'updatePackageItem'])->name('packages.item.update');

    Route::resource('packages', PackageController::class);

    Route::prefix('packages')->group(function () {
        Route::get('{package}', [PackageController::class, 'show'])->name('packages.show');
        Route::get('{package}/edit-relations', [PackageController::class, 'editRelations'])->name('packages.edit-relations');
        Route::post('{package}/update-relations', [PackageController::class, 'updateRelations'])->name('packages.update-relations');

        Route::get('/{package}/json', [PackageController::class, 'apiShow']);
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
        'pickup-points' => \App\Http\Controllers\PickupPointController::class,
    ]);

    /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
