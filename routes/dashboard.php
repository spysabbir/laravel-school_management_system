<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\RoleAndPermissionController;
use App\Http\Controllers\Dashboard\RoutineController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\User\AdminController;
use App\Http\Controllers\Dashboard\User\TeacherController;
use App\Http\Controllers\Dashboard\User\StaffController;
use App\Http\Controllers\Dashboard\User\StudentController;
use App\Http\Controllers\Dashboard\User\ParentController;
use App\Http\Controllers\Dashboard\Account\PasswordController;
use App\Http\Controllers\Dashboard\Account\ProfileController;
use App\Http\Controllers\Dashboard\AttendanceController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\ClasseController;
use App\Http\Controllers\Dashboard\DesignationController;
use App\Http\Controllers\Dashboard\ExamController;
use App\Http\Controllers\Dashboard\ExpenseCategoryController;
use App\Http\Controllers\Dashboard\ExpenseController;
use App\Http\Controllers\Dashboard\ShiftController;
use App\Http\Controllers\Dashboard\GroupController;
use App\Http\Controllers\Dashboard\SubjectController;
use Illuminate\Support\Facades\Route;

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
    Route::resource('admins', AdminController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('staffs', StaffController::class);
    Route::resource('students', StudentController::class);
    Route::resource('parents', ParentController::class);

    Route::resource('classes', ClasseController::class)->except(['show']);
    Route::resource('shifts', ShiftController::class)->except(['show']);
    Route::resource('groups', GroupController::class)->except(['show']);
    Route::resource('attendances', AttendanceController::class)->except(['show']);
    Route::resource('routines', RoutineController::class)->except(['show']);
    Route::resource('designations', DesignationController::class)->except(['show']);

    Route::resource('expense-categories', ExpenseCategoryController::class)->except(['show']);
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::resource('subjects', SubjectController::class)->except(['show']);
    Route::resource('exams', ExamController::class)->except(['show']);

    Route::get('setting/general', [SettingController::class, 'generalSetting'])->name('general.setting');
    Route::post('setting/general/update', [SettingController::class, 'updateGeneral'])->name('general.setting.update');
    Route::get('setting/mail', [SettingController::class, 'mailSetting'])->name('mail.setting');
    Route::post('setting/mail/update', [SettingController::class, 'updateMail'])->name('mail.setting.update');
    Route::get('setting/sms', [SettingController::class, 'smsSetting'])->name('sms.setting');
    Route::post('setting/sms/update', [SettingController::class, 'updateSms'])->name('sms.setting.update');
    Route::get('setting/payment', [SettingController::class, 'paymentSetting'])->name('payment.setting');
    Route::post('setting/payment/update', [SettingController::class, 'updatePayment'])->name('payment.setting.update');

    Route::redirect('account', 'profile/edit')->name('account');

    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('password/edit', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('password/update', [PasswordController::class, 'update'])->name('password.update');

    Route::get('appearance', [DashboardController::class, 'appearance'])->name('appearance');
});
