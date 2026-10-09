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

Route::get('/barang/{id}', function ($id) {
    $item = (object) [
        'id' => $id,
        'title' => 'iPhone 13 Pro Max Abu-abu',
        'kategori' => 'elektronik',
        'status' => 'tersedia', // ganti 'diklaim' untuk lihat state tidak tersedia
        'location' => 'Perpustakaan Pusat Lantai 2, Meja Sudut',
        'found_at' => now()->subDay(),
        'reporter_name' => 'Alif Ramadhan (Fakultas Hukum)',
        'description' => 'Ditemukan sebuah iPhone 13 Pro Max warna graphite/abu-abu gelap. Kondisi HP menyala, menggunakan casing transparan dengan sedikit goresan di bagian sudut kiri bawah.',
        'image_url' => null,
        'thumbnails' => [],
    ];

    return view('pages.detail-barang', ['item' => $item]);
})->name('barang.detail');

Route::get('/barang/{id}/klaim', function ($id) {
    $item = (object) ['id' => $id, 'title' => 'iPhone 13 Pro Max Abu-abu'];
    return view('pages.form-klaim', ['item' => $item]);
})->name('klaim.create');

Route::post('/barang/{id}/klaim', function ($id) {
    request()->validate([
        'ciri_khusus' => 'required|string|min:10',
        'nama_pengklaim' => 'required|string|max:100',
        'whatsapp' => 'required|string|max:20',
        'email' => 'required|email',
    ]);

    return redirect()->route('klaim.create', $id)->with('submitted', true);
})->name('klaim.store');

// Rute untuk menampilkan form Lapor Temuan (Dari Kodemu)
Route::get('/lapor-temuan', function () {
    return view('pages.form-lapor'); // Pastikan ini mengarah ke form-lapor atau lapor-temuan sesuai nama filemu
})->name('lapor.create');

// Rute untuk menangani pengiriman data form Lapor Temuan (Dari Kodemu)
Route::post('/lapor-temuan', function () {
    // Validasi dummy untuk UI saat ini, akan disesuaikan saat Pertemuan 5 (CRUD)
    request()->validate([
        'nama_barang' => 'required|string|max:255',
        'deskripsi' => 'required|string|min:10',
        'kategori' => 'required|string',
        'lokasi' => 'required|string|max:255',
        'tanggal' => 'required|date',
        'nama_pelapor' => 'required|string|max:100',
        'kontak' => 'required|string|max:20',
        // 'foto' validation can be added later
    ]);

    // Redirect kembali ke form dengan pesan sukses
    return redirect()->route('lapor.create')->with('success', 'Laporan berhasil dikirim. Menunggu tinjauan admin.');
})->name('lapor.store');

/*
| Riwayat: laporan & klaim milik user yang login. (Dari Kode Temanmu)
| Status di database (pending/approved/rejected/completed) dipetakan ke
| nilai yang dikenali komponen status-badge.
| Belum ada fitur login, jadi tanpa user kedua daftar tampil kosong.
*/
Route::get('/riwayat', function () {
    $user = auth()->user();

    if (! $user) {
        return view('pages.riwayat', ['reports' => collect(), 'claims' => collect()]);
    }

    $reportStatus = [
        'pending'   => 'menunggu',
        'approved'  => 'tersedia',
        'rejected'  => 'ditolak',
        'completed' => 'diklaim',
    ];

    $claimStatus = [
        'pending'   => 'menunggu',
        'approved'  => 'disetujui',
        'rejected'  => 'ditolak',
        'completed' => 'selesai',
    ];

    $reports = $user->reports()->latest()->get()->map(fn ($report) => (object) [
        'id' => $report->id,
        'title' => $report->item_name,
        'submitted_at' => $report->created_at,
        'status' => $reportStatus[$report->status] ?? 'menunggu',
    ]);

    $claims = $user->claims()->with('report')->latest()->get()->map(fn ($claim) => (object) [
        'item_id' => $claim->report_id,
        'title' => $claim->report->item_name,
        'submitted_at' => $claim->created_at,
        'status' => $claimStatus[$claim->status] ?? 'menunggu',
    ]);

    return view('pages.riwayat', compact('reports', 'claims'));
})->name('riwayat');

// Rute untuk Halaman Auth (Mockup UI)
Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login');

Route::get('/register', function () {
    return view('pages.auth.register');
})->name('register');