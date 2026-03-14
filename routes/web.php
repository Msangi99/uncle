<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\OtherPaymentsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentInfoController;
use Illuminate\Support\Facades\Route;

// Login as first page when opening the project
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::put('/classes/{classe}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{classe}', [ClassController::class, 'destroy'])->name('classes.destroy');
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::post('/students/bulk-destroy', [StudentController::class, 'bulkDestroy'])->name('students.bulk-destroy');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/sample', [StudentController::class, 'downloadSample'])->name('students.sample');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('/student-info', [StudentInfoController::class, 'index'])->name('student-info.index');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/other-payments', [OtherPaymentsController::class, 'index'])->name('other-payments.index');
    Route::post('/other-payments', [OtherPaymentsController::class, 'store'])->name('other-payments.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    Route::post('/settings/sms', [SettingsController::class, 'storeSms'])->name('settings.sms.store');
    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/export', [ReportController::class, 'export'])->name('report.export');
    Route::get('/sms', [SmsController::class, 'index'])->name('sms.index');
    Route::get('/sms/log', [SmsController::class, 'log'])->name('sms.log');
    Route::post('/sms/log/clear', [SmsController::class, 'clearLog'])->name('sms.log.clear');
    Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
