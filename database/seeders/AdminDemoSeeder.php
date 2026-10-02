<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Location;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/*
| Data contoh khusus untuk mencoba Dashboard & Statistik admin.
| Sengaja TIDAK dipanggil dari DatabaseSeeder supaya tidak bentrok dengan
| seeder resmi (user oleh Arsha, kategori & lokasi oleh Ahmad).
|
| Jalankan: php artisan db:seed --class=AdminDemoSeeder
| Setiap dijalankan akan menambah 60 laporan baru.
*/
class AdminDemoSeeder extends Seeder
{
    public function run()
    {
        $users = collect([
            ['name' => 'Admin Demo', 'nim' => 'ADM-001', 'study_program' => 'Staf Lost & Found', 'email' => 'admin@demo.test', 'role' => 'admin'],
            ['name' => 'Rina Pratiwi', 'nim' => '22001001', 'study_program' => 'Teknik Informatika', 'email' => 'rina@demo.test', 'role' => 'user'],
            ['name' => 'Bagus Santoso', 'nim' => '22001002', 'study_program' => 'Manajemen', 'email' => 'bagus@demo.test', 'role' => 'user'],
            ['name' => 'Dewi Lestari', 'nim' => '22001003', 'study_program' => 'Hukum', 'email' => 'dewi@demo.test', 'role' => 'user'],
        ])->map(fn ($data) => User::firstOrCreate(
            ['email' => $data['email']],
            $data + ['password' => Hash::make('password')],
        ));

        $categories = collect(['Elektronik', 'Dompet & Tas', 'Dokumen & Kartu', 'Kunci', 'Pakaian', 'Aksesoris', 'Lainnya'])
            ->map(fn ($name) => Category::firstOrCreate(['name' => $name]));

        $locations = collect(['Perpustakaan Pusat', 'Kantin FT', 'Parkiran Gedung C', 'Masjid Kampus', 'Gedung Rektorat', 'Lab Komputer', 'Lapangan Olahraga'])
            ->map(fn ($name) => Location::firstOrCreate(['name' => $name]));

        $items = [
            'iPhone 13 Abu-abu', 'Dompet Kulit Coklat', 'KTM a.n. Rina', 'Kunci Motor Honda', 'Jaket Hoodie Hitam',
            'Charger Laptop Asus', 'Tas Ransel Biru', 'Kacamata Minus', 'Botol Minum Tumbler', 'Earphone Bluetooth',
            'Flashdisk 32GB', 'Jam Tangan Casio', 'Payung Lipat', 'Buku Catatan Kalkulus', 'SIM C',
        ];

        // Peluang tiap status (persen)
        $statuses = ['pending' => 20, 'approved' => 40, 'rejected' => 10, 'completed' => 30];

        for ($i = 0; $i < 60; $i++) {
            // Lebih banyak laporan di bulan-bulan terakhir supaya tren terlihat
            $createdAt = now()->subDays((int) floor(240 * (mt_rand(0, 1000) / 1000) ** 1.6))->setTime(mt_rand(7, 20), mt_rand(0, 59));
            $createdAt = $createdAt->min(now()); // jam acak hari ini tidak boleh di masa depan

            $report = Report::create([
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'location_id' => $locations->random()->id,
                'type' => mt_rand(0, 100) < 45 ? 'lost' : 'found',
                'item_name' => $items[array_rand($items)],
                'description' => 'Data contoh untuk dashboard admin.',
                'event_date' => $createdAt->copy()->subDays(mt_rand(0, 2))->toDateString(),
                'status' => $this->weightedRandom($statuses),
            ]);

            // created_at diisi manual karena Eloquent selalu mengisi "sekarang"
            $report->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

            if ($report->type === 'found' && in_array($report->status, ['approved', 'completed']) && mt_rand(0, 1)) {
                Claim::create([
                    'report_id' => $report->id,
                    'user_id' => $users->where('role', 'user')->random()->id,
                    'claim_description' => 'Saya pemilik barang ini.',
                    'proof_description' => 'Ada stiker nama di bagian belakang.',
                    'status' => $report->status === 'completed' ? 'completed' : 'pending',
                ]);
            }
        }
    }

    private function weightedRandom(array $weights): string
    {
        $roll = mt_rand(1, array_sum($weights));

        foreach ($weights as $value => $weight) {
            if (($roll -= $weight) <= 0) {
                return $value;
            }
        }

        return array_key_first($weights);
    }
}
