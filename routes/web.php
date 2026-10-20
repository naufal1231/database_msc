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
    Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

// Settings (bisa dilihat oleh admin & super_admin, update hanya super_admin)
Route::middleware(['auth', 'active', 'role:admin,super_admin'])->prefix('super-admin')->group(function () {
    Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
});

// Admin (Pelatih) Routes
Route::middleware(['auth', 'active', 'role:admin,super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::resource('athletes', \App\Http\Controllers\AthleteController::class);

    // Event History Routes
    Route::get('event-history', [\App\Http\Controllers\EventHistoryController::class, 'index'])->name('event-history.index');
    Route::get('athletes/{athlete}/event-history', [\App\Http\Controllers\EventHistoryController::class, 'show'])->name('event-history.show');

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

    // Dispensasi Atlet Routes
    Route::get('dispensasi', [\App\Http\Controllers\DispensationController::class, 'index'])->name('admin.dispensation.index');
    // Template routes HARUS sebelum {dispensation} wildcard
    Route::get('dispensasi/template', [\App\Http\Controllers\DispensationController::class, 'templatePage'])->name('admin.dispensation.template');
    Route::post('dispensasi/template', [\App\Http\Controllers\DispensationController::class, 'uploadTemplate'])->name('admin.dispensation.template.upload');
    Route::post('dispensasi/{dispensation}/approve', [\App\Http\Controllers\DispensationController::class, 'approve'])->name('admin.dispensation.approve');
    Route::post('dispensasi/{dispensation}/reject', [\App\Http\Controllers\DispensationController::class, 'reject'])->name('admin.dispensation.reject');
    Route::get('dispensasi/{dispensation}/pdf', [\App\Http\Controllers\DispensationController::class, 'downloadPdf'])->name('admin.dispensation.pdf');

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
    // Event History for parent (secured by parentShow method)
    Route::get('/event-history/{athlete}', [\App\Http\Controllers\EventHistoryController::class, 'parentShow'])->name('parent.event-history.show');

    // Dispensasi Atlet (Orang Tua)
    Route::get('/dispensasi', [\App\Http\Controllers\Parent\DispensationController::class, 'index'])->name('parent.dispensation.index');
    Route::get('/dispensasi/buat', [\App\Http\Controllers\Parent\DispensationController::class, 'create'])->name('parent.dispensation.create');
    Route::post('/dispensasi', [\App\Http\Controllers\Parent\DispensationController::class, 'store'])->name('parent.dispensation.store');
    Route::get('/dispensasi/{dispensation}/unduh', [\App\Http\Controllers\Parent\DispensationController::class, 'download'])->name('parent.dispensation.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
