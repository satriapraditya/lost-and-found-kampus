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

// Placeholder — implementasi belum dibuat di Pertemuan 4 ini
Route::get('/lapor', fn () => 'TODO: form-lapor.blade.php')->name('lapor.create');

/*
| Riwayat: laporan & klaim milik user yang login.
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
