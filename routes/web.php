<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/awal', function () {
    return view('awal');
});

Route::get('/', [ReportController::class, 'index'])->name('beranda');
Route::get('/beranda', [ReportController::class, 'index']);
Route::get('/barang/{report}', [ReportController::class, 'show'])->name('barang.detail');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/lapor-temuan', [ReportController::class, 'create'])->name('lapor.create');
    Route::post('/lapor-temuan', [ReportController::class, 'store'])->name('lapor.store');
    Route::get('/barang/{report}/klaim', [ClaimController::class, 'create'])->name('klaim.create');
    Route::post('/barang/{report}/klaim', [ClaimController::class, 'store'])->name('klaim.store');
    Route::get('/riwayat', [ReportController::class, 'history'])->name('riwayat');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::patch('/reports/{report}', [AdminController::class, 'updateReport'])->name('reports.update');
    Route::patch('/claims/{claim}', [AdminController::class, 'updateClaim'])->name('claims.update');
});
