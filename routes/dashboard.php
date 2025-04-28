<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\RoleAndPermissionController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::controller(RoleAndPermissionController::class)->group(function () {
        Route::get('roles-permissions', 'index')->name('roles.permissions');

        Route::get('roles-create', 'createRoles')->name('roles.create');
        Route::post('roles-store', 'storeRoles')->name('roles.store');
        Route::delete('roles-destroy/{id}', 'destroyRoles')->name('roles.destroy');

        Route::get('roles-permissions-assign/{id}', 'assign')->name('roles.permissions.assign');
        Route::put('roles-permissions-assign-store/{id}', 'assignStore')->name('roles.permissions.assign.store');
        Route::get('roles-permissions-revoke/{id}', 'revoke')->name('roles.permissions.revoke');
    });

    Route::resource('users', UserController::class);

    Route::redirect('account', 'profile/edit')->name('account.settings');

    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('password/edit', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('password/update', [PasswordController::class, 'update'])->name('password.update');

    Route::get('appearance', [DashboardController::class, 'appearance'])->name('appearance');
});
