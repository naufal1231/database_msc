<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\DashboardController;

Route::get('/waiting-approval', function () {
    return view('auth.waiting-approval');
})->middleware(['auth'])->name('waiting.approval');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'active'])
    ->name('dashboard');

// Super Admin Routes
Route::middleware(['auth', 'active', 'role:super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'superAdminDashboard'])->name('super_admin.dashboard');
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

// Admin (Pelatih) Routes
Route::middleware(['auth', 'active', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::resource('athletes', \App\Http\Controllers\AthleteController::class);

    // Reports Route
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');

    // Competitions & Entries & Results
    Route::resource('competitions', \App\Http\Controllers\CompetitionController::class);
    Route::get('competitions/{competition}/entries', [\App\Http\Controllers\CompetitionEntryController::class, 'create'])->name('competitions.entries.create');
    Route::post('competitions/{competition}/entries', [\App\Http\Controllers\CompetitionEntryController::class, 'store'])->name('competitions.entries.store');
    Route::get('competitions/{competition}/results', [\App\Http\Controllers\ResultController::class, 'edit'])->name('competitions.results.edit');
    Route::post('competitions/{competition}/results', [\App\Http\Controllers\ResultController::class, 'store'])->name('competitions.results.store');
    Route::get('competitions/{competition}/export', [\App\Http\Controllers\CompetitionController::class, 'exportExcel'])->name('competitions.export');

    // Parents verification/approval routes
    Route::post('parents/{user}/approve', [\App\Http\Controllers\ParentApprovalController::class, 'approve'])->name('parents.approve');
    Route::delete('parents/{user}/reject', [\App\Http\Controllers\ParentApprovalController::class, 'reject'])->name('parents.reject');

    // Attendance routes
    Route::get('absensi', [\App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('absensi', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('absensi/rekap', [\App\Http\Controllers\AttendanceController::class, 'recap'])->name('attendance.recap');
    Route::get('absensi/export/{month}/{year}/{class?}', [\App\Http\Controllers\AttendanceController::class, 'export'])
        ->name('attendance.export');
});

// User (Orang Tua) Routes
Route::middleware(['auth', 'active', 'role:user'])->prefix('parent')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'parentDashboard'])->name('parent.dashboard');
    Route::get('/absensi', [\App\Http\Controllers\ParentController::class, 'attendanceHistory'])->name('parent.attendance');
    Route::get('/absensi/{month}/{year}', [\App\Http\Controllers\ParentController::class, 'attendanceMonthly'])->name('parent.attendance.monthly');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
