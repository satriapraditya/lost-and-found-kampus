<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/awal', function () {
    return view('awal');
});

Route::get('/', [ReportController::class, 'index'])->name('beranda');
Route::get('/beranda', [ReportController::class, 'index'])->name('beranda.legacy');
Route::get('/barang/{report}', [ReportController::class, 'show'])->name('barang.detail');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/lapor-temuan', [ReportController::class, 'create'])->name('lapor.create');
    Route::post('/lapor-temuan', [ReportController::class, 'store'])->name('lapor.store');
    Route::get('/barang/{report}/klaim', [ClaimController::class, 'create'])->name('klaim.create');
    Route::post('/barang/{report}/klaim', [ClaimController::class, 'store'])->name('klaim.store');
    Route::get('/riwayat', [ReportController::class, 'history'])->name('riwayat');
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifikasi/{notification}/dibaca', [NotificationController::class, 'markRead'])->name('notifications.read');
});

// Login admin lewat form login biasa (tab "Administrator")
Route::redirect('/admin/login', '/login')->name('admin.login');

/*
|--------------------------------------------------------------------------
| Panel Admin
|--------------------------------------------------------------------------
| Halaman (Dashboard, Statistik, Barang Hilang/Temuan, Verifikasi Klaim,
| Kelola Admin, Daftar User) ada di App\Http\Controllers\Admin\*.
| Perubahan status laporan & klaim tetap lewat AdminController
| (admin.reports.update & admin.claims.update) supaya aturannya satu tempat.
| Nama rute halaman mengikuti menu di components/admin/sidebar.blade.php.
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('index');

        // Ubah status (setujui / tolak / selesai)
        Route::patch('/reports/{report}', [AdminController::class, 'updateReport'])->name('reports.update');
        Route::patch('/claims/{claim}', [AdminController::class, 'updateClaim'])->name('claims.update');

        // Antrean tinjauan sederhana (halaman lama, isinya juga ada di Dashboard)
        Route::get('/antrean', [AdminController::class, 'index'])->name('queue');

        Route::controller(\App\Http\Controllers\Admin\StatisticsController::class)->prefix('statistik')->group(function () {
            Route::get('/', 'index')->name('statistics');
            Route::get('/export/excel', 'exportExcel')->name('statistics.excel');
            Route::get('/export/pdf', 'print')->name('statistics.pdf');
        });

        // Barang Hilang & Temuan — satu controller, dibedakan type
        Route::controller(\App\Http\Controllers\Admin\ReportController::class)->group(function () {
            Route::get('/barang-hilang', 'index')->defaults('type', 'lost')->name('lost.index');
            Route::get('/barang-hilang/{report}', 'show')->name('lost.show');
            Route::get('/barang-temuan', 'index')->defaults('type', 'found')->name('found.index');
            Route::get('/barang-temuan/{report}', 'show')->name('found.show');

            Route::patch('/laporan/{report}/selesai', 'complete')->name('reports.complete');
            Route::delete('/laporan/{report}', 'destroy')->name('reports.destroy');
        });

        // Verifikasi Klaim
        Route::get('/klaim', [\App\Http\Controllers\Admin\ClaimController::class, 'index'])->name('claims.index');
        Route::get('/klaim/{claim}', [\App\Http\Controllers\Admin\ClaimController::class, 'show'])->name('claims.show');

        // Kelola Admin
        Route::controller(\App\Http\Controllers\Admin\AdminController::class)->prefix('kelola-admin')->name('admins.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tambah', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::patch('/{user}/jadikan-admin', 'promote')->name('promote');
            Route::patch('/{user}/cabut', 'demote')->name('demote');
        });

        // Daftar User
        Route::get('/user', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::delete('/user/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    });
