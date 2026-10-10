<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportHistory;
use App\Services\ReportStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/*
| Kelola laporan Barang Hilang & Barang Temuan. Keduanya tabel yang sama
| (reports), dibedakan kolom type — rute admin.lost.* mengirim type 'lost',
| admin.found.* mengirim type 'found'.
|
| Alur status: pending → approved / rejected (AdminController@updateReport,
| rute admin.reports.update), lalu approved → completed (method complete di
| sini). Barang temuan yang klaimnya disetujui menjadi 'claimed' dan selesai
| lewat halaman Verifikasi Klaim.
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

    /*
    | Tandai selesai untuk laporan yang sudah tayang tanpa lewat klaim,
    | misalnya barang hilang yang sudah ditemukan pemiliknya sendiri.
    */
    public function complete(Request $request, Report $report)
    {
        $completed = DB::transaction(function () use ($request, $report) {
            $report = Report::whereKey($report->id)->lockForUpdate()->firstOrFail();

            if ($report->status !== 'approved') {
                return false;
            }

            $report->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            ReportHistory::create([
                'report_id' => $report->id,
                'user_id' => $request->user()->id,
                'status' => 'completed',
                'note' => 'Barang sudah kembali ke pemilik.',
            ]);

            Notification::create([
                'user_id' => $report->user_id,
                'title' => 'Laporan selesai',
                'message' => 'Laporan "' . $report->item_name . '" ditandai selesai. Terima kasih!',
                'type' => 'report',
            ]);

            return true;
        });

        return $completed
            ? back()->with('success', 'Laporan ditandai selesai.')
            : back()->with('error', 'Hanya laporan yang sudah disetujui yang bisa ditandai selesai.');
    }

    public function destroy(Report $report)
    {
        $route = $report->type === 'lost' ? 'admin.lost.index' : 'admin.found.index';

        Storage::disk('public')->delete($report->images->pluck('image_path')->all());
        $report->delete(); // gambar, klaim, dan riwayat ikut terhapus (cascade)

        return redirect()->route($route)->with('success', "Laporan \"{$report->item_name}\" dihapus.");
    }
}
