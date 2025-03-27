<?php

use App\Http\Controllers\Dashboard\RoleAndPermissionController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Dashboard\DashboardController;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('roles-and-permissions', [RoleAndPermissionController::class,'index'])->name('roles-and-permissions');
    Route::post('role-store', [RoleAndPermissionController::class,'roleStore'])->name('role.store');
    Route::delete('role-destroy/{id}', [RoleAndPermissionController::class,'roleDestroy'])->name('role.destroy');
    Route::get('roles-and-permissions/assign/{id}', [RoleAndPermissionController::class, 'assign'])->name('roles-and-permissions.assign');
    Route::post('roles-and-permissions/assign/{id}', [RoleAndPermissionController::class, 'assignPermission'])->name('roles-and-permissions.assignPermission');
    Route::delete('roles-and-permissions/revoke/{id}', [RoleAndPermissionController::class, 'revoke'])->name('roles-and-permissions.revoke');

    Route::resource('users', UserController::class);

    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
