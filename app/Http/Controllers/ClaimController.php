<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ClaimHistory;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimController extends Controller
{
    public function create(Report $report)
    {
        abort_unless($report->status === 'approved', 404);
        abort_if($report->user_id === auth()->id(), 403);

        $item = (object) ['id' => $report->id, 'title' => $report->item_name];

        if (! session()->has('submitted')
            && $report->claims()->where('user_id', auth()->id())->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->route('riwayat');
        }

        return view('pages.form-klaim', compact('item'));
    }

    public function store(Request $request, Report $report)
    {
        abort_unless($report->status === 'approved', 404);
        abort_if($report->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'ciri_khusus' => ['required', 'string', 'min:10', 'max:5000'],
            'whatsapp' => ['required', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($request, $report, $data) {
            $report = Report::query()->whereKey($report->id)->lockForUpdate()->firstOrFail();
            abort_unless($report->status === 'approved', 409, 'Barang sudah tidak tersedia untuk diklaim.');

            if ($report->claims()->where('user_id', $request->user()->id)->whereIn('status', ['pending', 'approved'])->exists()) {
                throw ValidationException::withMessages([
                    'ciri_khusus' => 'Anda sudah memiliki klaim aktif untuk barang ini.',
                ]);
            }

            $claim = $report->claims()->create([
                'user_id' => $request->user()->id,
                'claim_description' => 'Permohonan verifikasi kepemilikan.',
                'proof_description' => $data['ciri_khusus'],
                'whatsapp' => $data['whatsapp'],
                'status' => 'pending',
            ]);

            ClaimHistory::create([
                'claim_id' => $claim->id,
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'note' => 'Klaim dikirim dan menunggu verifikasi admin.',
            ]);

            Notification::create([
                'user_id' => $report->user_id,
                'title' => 'Klaim baru untuk barang Anda',
                'message' => $request->user()->name.' mengajukan klaim untuk '.$report->item_name.'.',
                'type' => 'claim',
            ]);
        });

        return redirect()
            ->route('klaim.create', $report)
            ->with('submitted', true);
    }
}
