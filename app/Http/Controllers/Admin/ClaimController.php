<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Notification;
use App\Services\ReportStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
| Verifikasi klaim kepemilikan barang temuan.
|
| Alur status: pending → approved / rejected, lalu approved → completed
| (barang sudah diserahkan). Saat klaim selesai, laporannya ikut 'completed'.
*/
class ClaimController extends Controller
{
    // Status klaim di database → nilai yang dikenali komponen <x-status-badge>
    public const BADGE = [
        'pending' => 'menunggu',
        'approved' => 'disetujui',
        'rejected' => 'ditolak',
        'completed' => 'selesai',
    ];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:100',
            'status' => 'nullable|in:' . implode(',', array_keys(ReportStatistics::STATUSES)),
        ]);

        $claims = Claim::query()
            ->with(['report:id,item_name,type,category_id', 'report.category:id,name', 'user:id,name,nim'])
            ->when($filters['q'] ?? null, function ($q, $search) {
                $like = '%' . mb_strtolower($search) . '%';

                $q->where(fn ($q) => $q
                    ->whereHas('report', fn ($q) => $q->whereRaw('LOWER(item_name) LIKE ?', [$like]))
                    ->orWhereHas('user', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', [$like])));
            })
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.claims.index', [
            'filters' => $filters,
            'claims' => $claims,
            'statusCounts' => Claim::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Claim $claim)
    {
        $claim->load([
            'user:id,name,nim,study_program,email',
            'report.category:id,name',
            'report.location:id,name',
            'report.user:id,name',
            'report.images',
        ]);

        // Klaim lain untuk barang yang sama, supaya admin bisa membandingkan
        $otherClaims = Claim::with('user:id,name')
            ->where('report_id', $claim->report_id)
            ->whereKeyNot($claim->id)
            ->latest()
            ->get();

        return view('admin.claims.show', compact('claim', 'otherClaims'));
    }

    public function approve(Request $request, Claim $claim)
    {
        if ($claim->status !== 'pending') {
            return back()->with('error', 'Hanya klaim berstatus Menunggu yang bisa disetujui.');
        }

        $validated = $request->validate(['admin_note' => 'nullable|string|max:500']);
        $note = $validated['admin_note'] ?? null;

        DB::transaction(function () use ($claim, $note) {
            $claim->update(['status' => 'approved', 'admin_note' => $note]);

            // Satu barang hanya punya satu pemilik: klaim lain yang masih menunggu otomatis ditolak
            $claim->report->claims()
                ->where('status', 'pending')
                ->whereKeyNot($claim->id)
                ->get()
                ->each(function (Claim $other) {
                    $other->update(['status' => 'rejected', 'admin_note' => 'Barang sudah diklaim oleh pemilik lain.']);
                    $this->notify($other, 'Klaim ditolak', "Klaim \"{$other->report->item_name}\" ditolak karena barang sudah diklaim oleh pemilik lain.");
                });
        });

        $this->notify($claim, 'Klaim disetujui', "Klaim \"{$claim->report->item_name}\" disetujui. Silakan ambil barang di pos Lost & Found dengan membawa KTM." . ($note ? " Catatan admin: {$note}" : ''));

        return back()->with('success', 'Klaim disetujui. Klaim lain untuk barang ini otomatis ditolak.');
    }

    public function reject(Request $request, Claim $claim)
    {
        if ($claim->status !== 'pending') {
            return back()->with('error', 'Hanya klaim berstatus Menunggu yang bisa ditolak.');
        }

        $validated = $request->validate([
            'admin_note' => 'required|string|min:5|max:500',
        ], [
            'admin_note.required' => 'Tulis alasan penolakan supaya pengklaim tahu alasannya.',
        ]);

        $claim->update(['status' => 'rejected'] + $validated);

        $this->notify($claim, 'Klaim ditolak', "Klaim \"{$claim->report->item_name}\" ditolak: {$validated['admin_note']}");

        return back()->with('success', 'Klaim ditolak.');
    }

    public function complete(Claim $claim)
    {
        if ($claim->status !== 'approved') {
            return back()->with('error', 'Hanya klaim yang sudah disetujui yang bisa ditandai selesai.');
        }

        DB::transaction(function () use ($claim) {
            $claim->update(['status' => 'completed']);
            $claim->report->update(['status' => 'completed', 'completed_at' => now()]);
        });

        $this->notify($claim, 'Barang diserahkan', "Barang \"{$claim->report->item_name}\" sudah diserahkan kepadamu. Terima kasih!");

        return back()->with('success', 'Barang ditandai sudah diserahkan ke pemilik.');
    }

    private function notify(Claim $claim, string $title, string $message): void
    {
        Notification::create([
            'user_id' => $claim->user_id,
            'title' => $title,
            'message' => $message,
            'type' => 'claim',
        ]);
    }
}
