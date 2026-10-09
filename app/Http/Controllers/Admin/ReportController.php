<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Services\ReportStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
| Kelola laporan Barang Hilang & Barang Temuan. Keduanya tabel yang sama
| (reports), dibedakan kolom type — rute admin.lost.* mengirim type 'lost',
| admin.found.* mengirim type 'found'.
|
| Alur status: pending → approved / rejected, lalu approved → completed.
*/
class ReportController extends Controller
{
    public function index(Request $request, string $type)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:100',
            'status' => 'nullable|in:' . implode(',', array_keys(ReportStatistics::STATUSES)),
            'category_id' => 'nullable|integer',
        ]);

        $reports = Report::query()
            ->with(['category:id,name', 'location:id,name', 'user:id,name'])
            ->where('type', $type)
            ->when($filters['q'] ?? null, fn ($q, $search) => $q->whereRaw('LOWER(item_name) LIKE ?', ['%' . mb_strtolower($search) . '%']))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.reports.index', [
            'type' => $type,
            'filters' => $filters,
            'reports' => $reports,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'statusCounts' => Report::where('type', $type)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function show(Request $request, Report $report)
    {
        // /barang-hilang/{id} hanya untuk laporan hilang, begitu juga sebaliknya
        $type = $request->routeIs('admin.lost.*') ? 'lost' : 'found';
        abort_if($report->type !== $type, 404);

        $report->load([
            'category:id,name',
            'location:id,name',
            'user:id,name,nim,study_program,email',
            'images',
            'claims.user:id,name,nim',
            'histories' => fn ($q) => $q->latest()->with('user:id,name'),
        ]);

        return view('admin.reports.show', compact('report', 'type'));
    }

    public function approve(Report $report)
    {
        if ($report->status !== 'pending') {
            return back()->with('error', 'Hanya laporan berstatus Menunggu yang bisa disetujui.');
        }

        $report->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->record($report, 'Laporan disetujui dan ditampilkan di situs.');
        $this->notify($report, 'Laporan disetujui', "Laporan \"{$report->item_name}\" sudah disetujui admin dan tampil di situs.");

        return back()->with('success', 'Laporan disetujui.');
    }

    public function reject(Request $request, Report $report)
    {
        if ($report->status !== 'pending') {
            return back()->with('error', 'Hanya laporan berstatus Menunggu yang bisa ditolak.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'Tulis alasan penolakan supaya pelapor tahu yang perlu diperbaiki.',
        ]);

        $report->update(['status' => 'rejected'] + $validated);

        $this->record($report, $validated['rejection_reason']);
        $this->notify($report, 'Laporan ditolak', "Laporan \"{$report->item_name}\" ditolak: {$validated['rejection_reason']}");

        return back()->with('success', 'Laporan ditolak.');
    }

    public function complete(Report $report)
    {
        if ($report->status !== 'approved') {
            return back()->with('error', 'Hanya laporan yang sudah disetujui yang bisa ditandai selesai.');
        }

        $report->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->record($report, 'Barang sudah kembali ke pemilik.');
        $this->notify($report, 'Laporan selesai', "Laporan \"{$report->item_name}\" ditandai selesai. Terima kasih!");

        return back()->with('success', 'Laporan ditandai selesai.');
    }

    public function destroy(Report $report)
    {
        $route = $report->type === 'lost' ? 'admin.lost.index' : 'admin.found.index';

        Storage::disk('public')->delete($report->images->pluck('image_path')->all());
        $report->delete(); // gambar, klaim, dan riwayat ikut terhapus (cascade)

        return redirect()->route($route)->with('success', "Laporan \"{$report->item_name}\" dihapus.");
    }

    /*
    | Riwayat butuh user_id admin. Selama area admin belum memakai login
    | (lihat TODO di routes/web.php), riwayat dilewati.
    */
    private function record(Report $report, string $note): void
    {
        if (! auth()->check()) {
            return;
        }

        ReportHistory::create([
            'report_id' => $report->id,
            'user_id' => auth()->id(),
            'status' => $report->status,
            'note' => $note,
        ]);
    }

    private function notify(Report $report, string $title, string $message): void
    {
        Notification::create([
            'user_id' => $report->user_id,
            'title' => $title,
            'message' => $message,
            'type' => 'report',
        ]);
    }
}
