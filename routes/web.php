<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\UserController;
use Livewire\Volt\Volt;
use App\Http\Controllers\PackageTypeController;
use App\Http\Controllers\PackageCategoryController;
use App\Http\Controllers\DifficultyTypeController;
use App\Http\Controllers\RoleController;

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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('package-types', PackageTypeController::class);
    Route::resource('package-categories', PackageCategoryController::class);
    Route::resource('difficulty-types', DifficultyTypeController::class);
    Route::resource('roles', RoleController::class);

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
