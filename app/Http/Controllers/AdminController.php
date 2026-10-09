<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\ClaimHistory;
use App\Models\Notification;
use App\Models\Report;
use App\Models\ReportHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'category', 'location', 'images'])
            ->where('status', 'pending')
            ->oldest()
            ->get();
        $claims = Claim::with(['user', 'report'])
            ->whereIn('status', ['pending', 'approved'])
            ->oldest()
            ->get();

        return view('pages.admin.index', compact('reports', 'claims'));
    }

    public function updateReport(Request $request, Report $report)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $report, $data) {
            $report = Report::whereKey($report->id)->lockForUpdate()->firstOrFail();
            abort_unless($report->status === 'pending', 409, 'Laporan ini sudah ditinjau.');

            $report->update([
                'status' => $data['status'],
                'rejection_reason' => $data['status'] === 'rejected' ? ($data['note'] ?? 'Laporan ditolak oleh admin.') : null,
                'approved_at' => $data['status'] === 'approved' ? now() : null,
            ]);

            ReportHistory::create([
                'report_id' => $report->id,
                'user_id' => $request->user()->id,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
            ]);

            Notification::create([
                'user_id' => $report->user_id,
                'title' => $data['status'] === 'approved' ? 'Laporan disetujui' : 'Laporan ditolak',
                'message' => $data['status'] === 'approved'
                    ? 'Laporan "'.$report->item_name.'" telah dipublikasikan.'
                    : 'Laporan "'.$report->item_name.'" ditolak. '.($data['note'] ?? ''),
                'type' => 'report',
            ]);
        });

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function updateClaim(Request $request, Claim $claim)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected', 'completed'])],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $claim, $data) {
            $report = Report::whereKey($claim->report_id)->lockForUpdate()->firstOrFail();
            $claim = Claim::whereKey($claim->id)->lockForUpdate()->firstOrFail();

            abort_unless(
                ($data['status'] === 'completed' && $claim->status === 'approved')
                || ($data['status'] !== 'completed' && $claim->status === 'pending'),
                409,
                'Status klaim ini sudah berubah.'
            );

            if ($data['status'] === 'approved') {
                abort_unless($report->status === 'approved', 409, 'Barang tidak lagi tersedia.');

                $report->update(['status' => 'claimed']);

                $otherClaims = Claim::where('report_id', $report->id)
                    ->where('id', '!=', $claim->id)
                    ->where('status', 'pending')
                    ->get();

                foreach ($otherClaims as $otherClaim) {
                    $otherClaim->update([
                        'status' => 'rejected',
                        'admin_note' => 'Klaim lain telah disetujui untuk barang ini.',
                    ]);
                    ClaimHistory::create([
                        'claim_id' => $otherClaim->id,
                        'user_id' => $request->user()->id,
                        'status' => 'rejected',
                        'note' => 'Klaim lain telah disetujui untuk barang ini.',
                    ]);
                    Notification::create([
                        'user_id' => $otherClaim->user_id,
                        'title' => 'Klaim tidak disetujui',
                        'message' => 'Barang "'.$report->item_name.'" telah diklaim oleh pengguna lain.',
                        'type' => 'claim',
                    ]);
                }
            } elseif ($data['status'] === 'completed') {
                $report->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            $claim->update([
                'status' => $data['status'],
                'admin_note' => $data['note'] ?? null,
            ]);

            ClaimHistory::create([
                'claim_id' => $claim->id,
                'user_id' => $request->user()->id,
                'status' => $data['status'],
                'note' => $data['note'] ?? null,
            ]);

            if ($data['status'] === 'approved') {
                ReportHistory::create([
                    'report_id' => $report->id,
                    'user_id' => $request->user()->id,
                    'status' => 'claimed',
                    'note' => 'Klaim disetujui untuk '.$claim->user->name.'.',
                ]);
            } elseif ($data['status'] === 'completed') {
                ReportHistory::create([
                    'report_id' => $report->id,
                    'user_id' => $request->user()->id,
                    'status' => 'completed',
                    'note' => 'Barang telah diserahkan kepada pemilik.',
                ]);
            }

            Notification::create([
                'user_id' => $claim->user_id,
                'title' => match ($data['status']) {
                    'approved' => 'Klaim disetujui',
                    'rejected' => 'Klaim ditolak',
                    default => 'Barang telah diserahkan',
                },
                'message' => 'Status klaim untuk "'.$report->item_name.'" diperbarui menjadi '.$data['status'].'. '.($data['note'] ?? ''),
                'type' => 'claim',
            ]);
        });

        return back()->with('success', 'Status klaim berhasil diperbarui.');
    }
}
