<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use Illuminate\Http\Request;

/*
| Halaman Verifikasi Klaim (daftar & detail).
|
| Perubahan status klaim (setujui / tolak / selesai) diproses oleh
| AdminController@updateClaim lewat rute admin.claims.update, supaya aturan
| statusnya hanya ada di satu tempat.
*/
class ClaimController extends Controller
{
    public const STATUSES = [
        'pending' => 'Menunggu',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'completed' => 'Selesai',
    ];

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
            'status' => 'nullable|in:' . implode(',', array_keys(self::STATUSES)),
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
            'user:id,name,nim,study_program,email,phone',
            'report.category:id,name',
            'report.location:id,name',
            'report.user:id,name',
            'report.images',
            'histories' => fn ($q) => $q->latest()->with('user:id,name'),
        ]);

        // Klaim lain untuk barang yang sama, supaya admin bisa membandingkan
        $otherClaims = Claim::with('user:id,name')
            ->where('report_id', $claim->report_id)
            ->whereKeyNot($claim->id)
            ->latest()
            ->get();

        return view('admin.claims.show', compact('claim', 'otherClaims'));
    }
}
