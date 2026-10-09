<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/*
| Semua hitungan untuk Dashboard Admin & halaman Statistik dikumpulkan di sini
| supaya controller tetap tipis dan angka di dashboard, statistik, dan file
| export selalu sama.
|
| Kesepakatan nilai kolom (dipakai juga oleh fitur Ahmad & Adit):
|   reports.type   : 'lost' (barang hilang) | 'found' (barang temuan)
|   reports.status : 'pending' | 'approved' | 'rejected' | 'completed'
|   claims.status  : 'pending' | 'approved' | 'rejected' | 'completed'
*/
class ReportStatistics
{
    public const TYPES = [
        'lost' => 'Barang Hilang',
        'found' => 'Barang Temuan',
    ];

    public const STATUSES = [
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'completed' => 'Selesai',
    ];

    // Status laporan di database → nilai yang dikenali komponen <x-status-badge>
    public const BADGE = [
        'pending' => 'menunggu',
        'approved' => 'tersedia',
        'rejected' => 'ditolak',
        'completed' => 'diklaim',
    ];

    private const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    private const WEEKDAYS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function summary(): array
    {
        $startOfMonth = now()->startOfMonth();

        $lost = Report::where('type', 'lost');
        $found = Report::where('type', 'found');

        $returned = Report::where('status', 'completed')->count();

        return [
            'lost' => (clone $lost)->count(),
            'lost_this_month' => (clone $lost)->where('created_at', '>=', $startOfMonth)->count(),
            'found' => (clone $found)->count(),
            'found_this_month' => (clone $found)->where('created_at', '>=', $startOfMonth)->count(),
            'pending_claims' => Claim::where('status', 'pending')->count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'returned' => $returned,
            // Persentase semua laporan (hilang & temuan) yang sudah selesai
            'return_rate' => $this->percent($returned, Report::count()),
        ];
    }

    /**
     * Jumlah laporan hilang vs temuan per bulan, $months bulan terakhir.
     */
    public function monthlyTrend(int $months = 8): array
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        $reports = Report::where('created_at', '>=', $start)->get(['type', 'status', 'created_at']);

        return $this->groupByMonth($reports, $start, now()->startOfMonth());
    }

    /**
     * Sebaran laporan per kategori. Kategori di luar $limit teratas
     * digabung menjadi "Lainnya" supaya warna grafik tidak kebanyakan.
     */
    public function categoryDistribution(int $limit = 4): Collection
    {
        $rows = Category::withCount('reports')
            ->orderByDesc('reports_count')
            ->get()
            ->where('reports_count', '>', 0)
            ->map(fn ($category) => ['label' => $category->name, 'total' => $category->reports_count]);

        // Kategori bernama "Lainnya" (kalau ada) ikut digabung ke kelompok "Lainnya"
        [$others, $rows] = $rows->partition(fn ($row) => $row['label'] === 'Lainnya');
        $rows = $rows->values();

        // Paling banyak $limit + 1 irisan = jumlah warna di CATEGORICAL (admin.js)
        if ($rows->count() > ($others->isEmpty() ? $limit + 1 : $limit)) {
            $others = $others->concat($rows->slice($limit));
            $rows = $rows->take($limit);
        }

        if ($others->isNotEmpty()) {
            $rows->push(['label' => 'Lainnya', 'total' => $others->sum('total')]);
        }

        $sum = $rows->sum('total');

        return $rows->map(fn ($row) => $row + ['percent' => $this->percent($row['total'], $sum)])->values();
    }

    public function topLocations(int $limit = 5): Collection
    {
        return Report::query()
            ->selectRaw('location_id, COUNT(*) as total')
            ->groupBy('location_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->with('location:id,name')
            ->get()
            ->map(fn ($row) => ['label' => $row->location->name ?? '—', 'total' => (int) $row->total]);
    }

    public function latestReports(int $limit = 5): Collection
    {
        return Report::with(['category:id,name', 'location:id,name', 'user:id,name'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Halaman Statistik & Export
    |--------------------------------------------------------------------------
    */

    /**
     * Query laporan sesuai filter dari form Statistik.
     * Filter: from, to (tanggal laporan masuk), type, category_id, status.
     */
    public function filteredQuery(array $filters): Builder
    {
        return Report::query()
            ->with(['category:id,name', 'location:id,name', 'user:id,name'])
            ->when($filters['from'] ?? null, fn ($q, $from) => $q->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($q, $to) => $q->whereDate('created_at', '<=', $to))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status));
    }

    public function filteredSummary(Collection $reports): array
    {
        $returned = $reports->where('status', 'completed')->count();

        return [
            'total' => $reports->count(),
            'lost' => $reports->where('type', 'lost')->count(),
            'found' => $reports->where('type', 'found')->count(),
            'returned' => $returned,
            'return_rate' => $this->percent($returned, $reports->count()),
        ];
    }

    /**
     * Tren bulanan dari laporan yang sudah difilter. Rentang bulan mengikuti
     * filter tanggal; tanpa filter, dipakai 12 bulan terakhir.
     */
    public function filteredTrend(Collection $reports, array $filters): array
    {
        $end = ! empty($filters['to']) ? Carbon::parse($filters['to'])->startOfMonth() : now()->startOfMonth();
        $start = ! empty($filters['from'])
            ? Carbon::parse($filters['from'])->startOfMonth()
            : $end->copy()->subMonths(11);

        // Rentang yang sangat panjang dipotong supaya grafik tetap terbaca
        if ($start->diffInMonths($end) > 23) {
            $start = $end->copy()->subMonths(23);
        }

        return $this->groupByMonth($reports, $start, $end);
    }

    /**
     * Jumlah laporan masuk per hari dalam seminggu (Senin–Minggu).
     */
    public function weekdayDistribution(Collection $reports): array
    {
        $counts = array_fill(0, 7, 0);

        foreach ($reports as $report) {
            // dayOfWeekIso: 1 = Senin … 7 = Minggu
            $counts[$report->created_at->dayOfWeekIso - 1]++;
        }

        return ['labels' => self::WEEKDAYS, 'data' => $counts];
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    private function groupByMonth(Collection $reports, Carbon $start, Carbon $end): array
    {
        $labels = [];
        $buckets = [];

        for ($month = $start->copy(); $month->lte($end); $month->addMonth()) {
            $key = $month->format('Y-m');
            $labels[] = self::MONTHS[$month->month - 1] . ($month->year !== now()->year ? ' ' . $month->format('y') : '');
            $buckets[$key] = ['lost' => 0, 'found' => 0, 'completed' => 0];
        }

        foreach ($reports as $report) {
            $key = $report->created_at->format('Y-m');

            if (! isset($buckets[$key])) {
                continue;
            }

            if (isset($buckets[$key][$report->type])) {
                $buckets[$key][$report->type]++;
            }

            if ($report->status === 'completed') {
                $buckets[$key]['completed']++;
            }
        }

        return [
            'labels' => $labels,
            'lost' => array_column($buckets, 'lost'),
            'found' => array_column($buckets, 'found'),
            'completed' => array_column($buckets, 'completed'),
        ];
    }

    private function percent(int $part, int $whole): int
    {
        return $whole > 0 ? (int) round($part / $whole * 100) : 0;
    }
}
