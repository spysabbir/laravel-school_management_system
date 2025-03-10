<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\Settings\ProfileController;
use App\Http\Controllers\Dashboard\Settings\PasswordController;



Route::middleware('auth')->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', [DashboardController::class, 'appearance'])->name('appearance');

    Route::get('roles', function () {
        return Inertia::render('Roles');
    })->middleware(['auth', 'verified'])->name('roles');

    Route::get('permissions', function () {
        return Inertia::render('Permissions');
    })->middleware(['auth', 'verified'])->name('permissions');

    Route::get('roles-permissions', function () {
        return Inertia::render('RolesPermissions');
    })->middleware(['auth', 'verified'])->name('roles-permissions');

    Route::get('users', function () {
        return Inertia::render('Users');
    })->middleware(['auth', 'verified'])->name('users');
});




