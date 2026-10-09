<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ReportStatistics;
use App\Support\SimpleXlsx;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function __construct(private ReportStatistics $stats)
    {
    }

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $reports = $this->stats->filteredQuery($filters)->get();

        return view('admin.statistik.index', [
            'filters' => $filters,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'summary' => $this->stats->filteredSummary($reports),
            'trend' => $this->stats->filteredTrend($reports, $filters),
            'weekdays' => $this->stats->weekdayDistribution($reports),
            'table' => $this->stats->filteredQuery($filters)->latest()->paginate(10)->withQueryString(),
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filters = $this->filters($request);
        $reports = $this->stats->filteredQuery($filters)->latest()->get();

        $rows = $reports->map(fn ($report) => [
            $report->id,
            $report->created_at->format('d/m/Y H:i'),
            ReportStatistics::TYPES[$report->type] ?? $report->type,
            $report->item_name,
            $report->category->name ?? '—',
            $report->location->name ?? '—',
            $report->event_date?->format('d/m/Y') ?? '—',
            $report->user->name ?? '—',
            ReportStatistics::STATUSES[$report->status] ?? $report->status,
        ]);

        $path = SimpleXlsx::write('Laporan', [
            'ID', 'Tanggal Masuk', 'Jenis', 'Nama Barang', 'Kategori', 'Lokasi',
            'Tanggal Kejadian', 'Pelapor', 'Status',
        ], $rows);

        return response()
            ->download($path, 'laporan-lost-found-' . now()->format('Y-m-d') . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->deleteFileAfterSend();
    }

    /*
    | "Export PDF" memakai halaman versi cetak yang otomatis membuka dialog
    | print browser — pilih "Simpan sebagai PDF". Cara ini tidak butuh
    | package PDF tambahan (dompdf dkk. butuh ekstensi GD).
    */
    public function print(Request $request)
    {
        $filters = $this->filters($request);
        $reports = $this->stats->filteredQuery($filters)->latest()->get();

        return view('admin.statistik.print', [
            'filters' => $filters,
            'categoryName' => isset($filters['category_id']) ? Category::find($filters['category_id'])?->name : null,
            'summary' => $this->stats->filteredSummary($reports),
            'reports' => $reports,
        ]);
    }

    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'type' => 'nullable|in:' . implode(',', array_keys(ReportStatistics::TYPES)),
            'category_id' => 'nullable|integer|exists:categories,id',
            'status' => 'nullable|in:' . implode(',', array_keys(ReportStatistics::STATUSES)),
        ], [
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        return array_filter($validated, fn ($value) => $value !== null && $value !== '');
    }
}
