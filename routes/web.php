<?php

use Illuminate\Support\Facades\Route;

Route::get('/awal', function () {
    return view('awal');
});

/*
|--------------------------------------------------------------------------
| Routes Pertemuan 4 — Lost & Found Kampus
|--------------------------------------------------------------------------
| Tempel ini ke routes/web.php yang sudah ada, atau require dari sana.
| Data $items dan $item di bawah ini contoh statis (dummy) — ganti
| dengan query Eloquent begitu model & migration-nya siap (Pertemuan 5).
*/

Route::get('/beranda', function () {
    // Dummy data biar halaman bisa langsung dicoba tanpa database.
    // Ganti dengan \App\Models\Barang::paginate(6) begitu model siap.
    $dummy = collect([
        (object) [
            'id' => 1,
            'title' => 'iPhone 13 Pro Max Abu-abu',
            'location' => 'Perpustakaan Lantai 2',
            'kategori' => 'elektronik',
            'status' => 'tersedia',
            'image_url' => null,
            'found_at' => now()->subDay(),
        ],
        (object) [
            'id' => 2,
            'title' => 'Kunci Motor Honda Hitam',
            'location' => 'Parkiran Gedung C FISIP',
            'kategori' => 'aksesoris',
            'status' => 'diklaim',
            'image_url' => null,
            'found_at' => now()->subDays(3),
        ],
    ]);

    return view('pages.beranda', ['items' => $dummy]);
})->name('beranda');

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
// Rute Login Admin (Mockup UI)
Route::get('/admin/login', function () {
    return view('pages.admin.login');
})->name('admin.login');
/*
|--------------------------------------------------------------------------
| Area Admin (Yosi)
|--------------------------------------------------------------------------
| Dashboard, Statistik, Barang Hilang/Temuan, Verifikasi Klaim, Kelola Admin,
| dan Daftar User. Nama rute mengikuti menu di sidebar
| (resources/views/components/admin/sidebar.blade.php).
| TODO: tambahkan ->middleware(['auth', 'admin']) begitu login &
| middleware admin selesai. Sementara ini terbuka supaya bisa dites.
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');

    Route::get('/statistik', [\App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics');
    Route::get('/statistik/export/excel', [\App\Http\Controllers\Admin\StatisticsController::class, 'exportExcel'])->name('statistics.excel');
    Route::get('/statistik/export/pdf', [\App\Http\Controllers\Admin\StatisticsController::class, 'print'])->name('statistics.pdf');

    // Barang Hilang & Temuan — satu controller, dibedakan type
    Route::controller(\App\Http\Controllers\Admin\ReportController::class)->group(function () {
        Route::get('/barang-hilang', 'index')->defaults('type', 'lost')->name('lost.index');
        Route::get('/barang-hilang/{report}', 'show')->name('lost.show');
        Route::get('/barang-temuan', 'index')->defaults('type', 'found')->name('found.index');
        Route::get('/barang-temuan/{report}', 'show')->name('found.show');

        Route::patch('/laporan/{report}/setujui', 'approve')->name('reports.approve');
        Route::patch('/laporan/{report}/tolak', 'reject')->name('reports.reject');
        Route::patch('/laporan/{report}/selesai', 'complete')->name('reports.complete');
        Route::delete('/laporan/{report}', 'destroy')->name('reports.destroy');
    });

    // Verifikasi Klaim
    Route::controller(\App\Http\Controllers\Admin\ClaimController::class)->prefix('klaim')->name('claims.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{claim}', 'show')->name('show');
        Route::patch('/{claim}/setujui', 'approve')->name('approve');
        Route::patch('/{claim}/tolak', 'reject')->name('reject');
        Route::patch('/{claim}/selesai', 'complete')->name('complete');
    });

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
