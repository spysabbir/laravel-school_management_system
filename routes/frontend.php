<?php

use App\Http\Controllers\Frontend\FrontendController;
use Illuminate\Support\Facades\Route;

// Frontend routes
Route::get('/', [FrontendController::class, 'index'])->name('index');
