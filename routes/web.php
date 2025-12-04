<?php

use App\Http\Controllers\CarController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\UserController;
use Livewire\Volt\Volt;
use App\Http\Controllers\PackageTypeController;
use App\Http\Controllers\PackageCategoryController;
use App\Http\Controllers\DifficultyTypeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\FollowupController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(when(Features::canManageTwoFactorAuthentication() && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'), ['password.confirm'], []))
        ->name('two-factor.show');
});
Route::get('/cars', function () {
    return \App\Models\Car::all();
});

Route::get('/hotels', function () {
    return \App\Models\Hotel::all();
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::post('/followup/store', [FollowupController::class, 'store'])->name('followup.store');

    Route::resource('users', UserController::class);
    Route::resource('cars', CarController::class);
    Route::resource('hotels', HotelController::class);
    Route::resource('packages', PackageController::class);
    Route::prefix('packages')->group(function () {
        Route::get('{package}', [PackageController::class, 'show'])->name('packages.show');
        Route::get('{package}/edit-relations', [PackageController::class, 'editRelations'])->name('packages.edit-relations');
        Route::post('{package}/update-relations', [PackageController::class, 'updateRelations'])->name('packages.update-relations');
    });
    Route::resource('pickup-points', \App\Http\Controllers\PickupPointController::class);
    Route::get('/leads/{lead}/assign', [LeadController::class, 'assignForm'])->name('leads.assign.form');
    Route::post('/leads/{lead}/assign', [LeadController::class, 'assignStore'])->name('leads.assign.store');
    Route::delete('/leads/assignment/{id}/delete', [LeadController::class, 'deleteAssignment'])->name('leads.assign.delete');

    Route::get('/leads/{lead}/details', [FollowupController::class, 'getLeadDetails']);
    Route::post('/leads/import', [LeadController::class, 'importLeads'])->name('leads.import');
    Route::get('/leads/{lead}/json', [LeadController::class, 'showJson']);
    Route::post('/leads/bulk-assign', [LeadController::class, 'bulkAssign'])->name('leads.bulkAssign');

    Route::resource('leads', LeadController::class);
    Route::resource('package-types', PackageTypeController::class);
    Route::resource('package-categories', PackageCategoryController::class);
    Route::resource('difficulty-types', DifficultyTypeController::class);
    Route::resource('roles', RoleController::class);
    Route::get('/packages/{package}/json', [PackageController::class, 'apiShow']);

    Route::post('/leads/send-package-email', [PackageController::class, 'sendPackageEmail'])->name('leads.sendPackageEmail');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/invoice', function () {
        return view('invoice.index');
    })->name('invoice.index');
});
