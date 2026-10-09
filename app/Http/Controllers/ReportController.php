<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Report;
use App\Models\ReportHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $items = Report::query()
            ->with(['category', 'location', 'images'])
            ->where('status', 'approved')
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($search) use ($request) {
                    $term = '%'.$request->input('q').'%';
                    $search->where('item_name', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhereHas('location', fn ($location) => $location->where('name', 'like', $term));
                });
            })
            ->when($request->filled('kategori') && $request->input('kategori') !== 'semua', function ($query) use ($request) {
                $query->whereHas('category', fn ($category) => $category->where('name', $request->input('kategori')));
            })
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(fn (Report $report) => $this->toCard($report));

        $categories = Category::query()->orderBy('name')->get();

        return view('pages.beranda', compact('items', 'categories'));
    }

    public function show(Report $report)
    {
        abort_unless(
            in_array($report->status, ['approved', 'claimed', 'completed'], true)
                || auth()->id() === $report->user_id
                || $report->claims()->where('user_id', auth()->id())->exists(),
            404
        );

        $report->load(['user', 'category', 'location', 'images']);
        $primaryImage = $report->images->sortByDesc('is_primary')->first();
        $item = (object) [
            'id' => $report->id,
            'title' => $report->item_name,
            'kategori' => $report->category->name,
            'status' => match ($report->status) {
                'approved' => 'tersedia',
                'claimed', 'completed' => 'diklaim',
                'rejected' => 'ditolak',
                default => 'menunggu',
            },
            'location' => $report->location->name,
            'found_at' => $report->event_date->copy()->setTimeFromTimeString($report->event_time ?? '00:00:00'),
            'reporter_name' => $report->user->name,
            'description' => $report->description,
            'image_url' => $primaryImage
                ? Storage::url($primaryImage->image_path)
                : null,
            'thumbnails' => $report->images
                ->map(fn ($image) => Storage::url($image->image_path))
                ->all(),
        ];

        return view('pages.detail-barang', compact('item'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('pages.form-lapor', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'min:10'],
            'kategori' => ['required', 'integer', 'exists:categories,id'],
            'lokasi' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $imagePath = null;
        if ($request->hasFile('foto')) {
            $imagePath = $request->file('foto')->store('reports', 'public');
            if ($imagePath === false) {
                throw new \RuntimeException('Foto laporan gagal disimpan.');
            }
        }

        try {
            $report = DB::transaction(function () use ($request, $data, $imagePath) {
                $location = Location::firstOrCreate(
                    ['name' => trim($data['lokasi'])],
                    ['description' => null]
                );

                $report = Report::create([
                    'user_id' => $request->user()->id,
                    'category_id' => $data['kategori'],
                    'location_id' => $location->id,
                    'type' => 'found',
                    'item_name' => $data['nama_barang'],
                    'description' => $data['deskripsi'],
                    'event_date' => $data['tanggal'],
                    'status' => 'pending',
                ]);

                if ($imagePath) {
                    $report->images()->create([
                        'image_path' => $imagePath,
                        'is_primary' => true,
                    ]);
                }

                ReportHistory::create([
                    'report_id' => $report->id,
                    'user_id' => $request->user()->id,
                    'status' => 'pending',
                    'note' => 'Laporan dikirim dan menunggu tinjauan admin.',
                ]);

                return $report;
            });
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('barang.detail', $report)
            ->with('success', 'Laporan tersimpan dan menunggu tinjauan admin.');
    }

    public function history()
    {
        $user = request()->user();
        $reportStatus = [
            'pending' => 'menunggu',
            'approved' => 'tersedia',
            'claimed' => 'diklaim',
            'rejected' => 'ditolak',
            'completed' => 'diklaim',
        ];
        $claimStatus = [
            'pending' => 'menunggu',
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            'completed' => 'selesai',
        ];

        $reports = $user->reports()->latest()->get()->map(fn (Report $report) => (object) [
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
    }

    private function toCard(Report $report)
    {
        $primaryImage = $report->images->sortByDesc('is_primary')->first();

        return (object) [
            'id' => $report->id,
            'title' => $report->item_name,
            'location' => $report->location->name,
            'kategori' => $report->category->name,
            'status' => 'tersedia',
            'image_url' => $primaryImage
                ? Storage::url($primaryImage->image_path)
                : null,
            'found_at' => $report->created_at,
        ];
    }
}
