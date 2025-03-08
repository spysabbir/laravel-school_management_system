<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
