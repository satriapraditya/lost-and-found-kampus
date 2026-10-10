@php
    use App\Http\Controllers\Admin\ClaimController;
    use App\Services\ReportStatistics;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $isLost = $type === 'lost';
    $route = $isLost ? 'admin.lost' : 'admin.found';
    $images = $report->images->sortByDesc('is_primary')->values();
    $imageUrl = fn ($path) => Str::startsWith($path, ['http://', 'https://']) ? $path : Storage::disk('public')->url($path);
    $card = 'flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6';
    $textareaClass = 'w-full resize-none rounded-sm border border-border bg-surface-white px-3 py-2.5 text-small text-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp

<x-layouts.admin :title="$report->item_name" :subtitle="ReportStatistics::TYPES[$type] . ' · dilaporkan ' . $report->created_at->locale('id')->diffForHumans()">
    <x-slot:actions>
        <x-button variant="secondary" :href="route($route . '.index')">
            <x-admin.icon name="arrow-left" class="h-4 w-4" />
            Kembali
        </x-button>
    </x-slot:actions>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="flex flex-col gap-6 xl:col-span-2">
            {{-- Detail barang --}}
            <section class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-card-title font-bold text-text">Detail Barang</h2>
                    <x-status-badge :status="ReportStatistics::BADGE[$report->status] ?? 'menunggu'" />
                </div>

                @if ($images->isNotEmpty())
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($images as $image)
                            <a href="{{ $imageUrl($image->image_path) }}" target="_blank" rel="noopener" class="block aspect-square overflow-hidden rounded-md border border-border bg-surface-muted">
                                <img src="{{ $imageUrl($image->image_path) }}" alt="Foto {{ $report->item_name }}" class="h-full w-full object-cover">
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="flex items-center justify-center gap-2 rounded-md border border-dashed border-border py-10 text-small text-text-muted">
                        <x-admin.icon name="photo" />
                        Tidak ada foto.
                    </div>
                @endif

                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-small sm:grid-cols-2">
                    <div>
                        <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Kategori</dt>
                        <dd class="mt-1 text-text">{{ $report->category->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Lokasi</dt>
                        <dd class="mt-1 text-text">{{ $report->location->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">{{ $isLost ? 'Waktu Hilang' : 'Waktu Ditemukan' }}</dt>
                        <dd class="mt-1 text-text">
                            {{ $report->event_date?->locale('id')->translatedFormat('l, d F Y') ?? '—' }}
                            @if ($report->event_time)
                                · {{ substr($report->event_time, 0, 5) }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Masuk</dt>
                        <dd class="mt-1 text-text">{{ $report->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Deskripsi</dt>
                        <dd class="mt-1 whitespace-pre-line text-text">{{ $report->description }}</dd>
                    </div>
                    @if ($report->special_features)
                        <div class="sm:col-span-2">
                            <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Ciri Khusus</dt>
                            <dd class="mt-1 whitespace-pre-line text-text">{{ $report->special_features }}</dd>
                        </div>
                    @endif
                    @if ($report->status === 'rejected' && $report->rejection_reason)
                        <div class="rounded-md bg-danger-bg px-4 py-3 text-danger-text sm:col-span-2">
                            <dt class="text-caption font-semibold uppercase tracking-wide">Alasan Ditolak</dt>
                            <dd class="mt-1">{{ $report->rejection_reason }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            {{-- Klaim (hanya barang temuan yang bisa diklaim) --}}
            @unless ($isLost)
                <section class="{{ $card }}">
                    <div>
                        <h2 class="text-card-title font-bold text-text">Klaim Kepemilikan</h2>
                        <p class="text-caption text-text-muted">{{ $report->claims->count() }} orang mengaku pemilik barang ini</p>
                    </div>

                    @forelse ($report->claims as $claim)
                        <a href="{{ route('admin.claims.show', $claim) }}" class="flex items-center justify-between gap-3 rounded-md border border-border px-4 py-3 text-small hover:bg-surface-muted">
                            <span>
                                <span class="font-semibold text-text">{{ $claim->user->name ?? '—' }}</span>
                                <span class="text-text-muted">· {{ $claim->created_at->locale('id')->diffForHumans() }}</span>
                            </span>
                            <x-status-badge :status="ClaimController::BADGE[$claim->status] ?? 'menunggu'" />
                        </a>
                    @empty
                        <p class="text-small text-text-muted">Belum ada klaim.</p>
                    @endforelse
                </section>
            @endunless

            {{-- Riwayat --}}
            @if ($report->histories->isNotEmpty())
                <section class="{{ $card }}">
                    <h2 class="text-card-title font-bold text-text">Riwayat</h2>
                    <ol class="flex flex-col gap-3 border-l-2 border-border pl-4">
                        @foreach ($report->histories as $history)
                            <li class="text-small">
                                <p class="font-semibold text-text">{{ ReportStatistics::STATUSES[$history->status] ?? $history->status }}</p>
                                @if ($history->note)
                                    <p class="text-text-secondary">{{ $history->note }}</p>
                                @endif
                                <p class="text-caption text-text-muted">{{ $history->user->name ?? '—' }} · {{ $history->created_at->locale('id')->translatedFormat('d M Y, H:i') }}</p>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </div>

        <div class="flex flex-col gap-6">
            {{-- Aksi --}}
            <section class="{{ $card }}">
                <h2 class="text-card-title font-bold text-text">Tindakan</h2>

                @if ($report->status === 'pending')
                    <p class="text-small text-text-secondary">Laporan ini belum tampil di situs. Periksa isinya, lalu setujui atau tolak.</p>

                    {{-- Diproses AdminController@updateReport (status + note) --}}
                    <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <x-button type="submit" variant="primary" class="w-full">Setujui Laporan</x-button>
                    </form>

                    <details class="group rounded-md border border-border" @if ($errors->has('note')) open @endif>
                        <summary class="cursor-pointer list-none px-4 py-2.5 text-center text-label font-semibold text-danger-text">Tolak Laporan…</summary>
                        <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="flex flex-col gap-3 border-t border-border p-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <label for="reject_note" class="text-small font-semibold text-text-secondary">Alasan penolakan</label>
                            <textarea id="reject_note" name="note" rows="3" required minlength="5" class="{{ $textareaClass }}" placeholder="Contoh: Foto tidak jelas, mohon unggah ulang.">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="text-small text-danger-text">{{ $message }}</p>
                            @enderror
                            <x-button type="submit" variant="danger" class="w-full">Kirim Penolakan</x-button>
                        </form>
                    </details>
                @elseif ($report->status === 'approved')
                    <p class="text-small text-text-secondary">
                        Laporan sudah tampil di situs{{ $report->approved_at ? ' sejak ' . $report->approved_at->locale('id')->translatedFormat('d M Y') : '' }}.
                        Tandai selesai kalau barang sudah kembali ke pemiliknya.
                    </p>
                    <form method="POST" action="{{ route('admin.reports.complete', $report) }}">
                        @csrf
                        @method('PATCH')
                        <x-button type="submit" variant="primary" class="w-full">Tandai Selesai</x-button>
                    </form>
                @elseif ($report->status === 'claimed')
                    @php $approvedClaim = $report->claims->firstWhere('status', 'approved'); @endphp
                    <p class="text-small text-text-secondary">
                        Klaim{{ $approvedClaim?->user ? ' dari ' . $approvedClaim->user->name : '' }} sudah disetujui. Barang menunggu diserahkan ke pemiliknya.
                    </p>
                    @if ($approvedClaim)
                        <x-button variant="primary" :href="route('admin.claims.show', $approvedClaim)" class="w-full">Buka Klaim</x-button>
                    @endif
                @elseif ($report->status === 'completed')
                    <p class="text-small text-text-secondary">
                        Barang sudah kembali ke pemilik{{ $report->completed_at ? ' pada ' . $report->completed_at->locale('id')->translatedFormat('d M Y') : '' }}.
                    </p>
                @else
                    <p class="text-small text-text-secondary">Laporan ini ditolak dan tidak tampil di situs.</p>
                @endif

                <form
                    method="POST"
                    action="{{ route('admin.reports.destroy', $report) }}"
                    onsubmit="return confirm('Hapus laporan ini? Foto, klaim, dan riwayatnya ikut terhapus.')"
                    class="border-t border-border pt-4"
                >
                    @csrf
                    @method('DELETE')
                    <x-button type="submit" variant="danger" class="w-full">
                        <x-admin.icon name="trash" class="h-4 w-4" />
                        Hapus Laporan
                    </x-button>
                </form>
            </section>

            {{-- Pelapor --}}
            <section class="{{ $card }}">
                <h2 class="text-card-title font-bold text-text">Pelapor</h2>
                @if ($report->user)
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-small font-bold text-primary">
                            {{ strtoupper(mb_substr($report->user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 text-small">
                            <p class="truncate font-semibold text-text">{{ $report->user->name }}</p>
                            <p class="truncate text-text-muted">{{ $report->user->nim }} · {{ $report->user->study_program }}</p>
                        </div>
                    </div>
                    <a href="mailto:{{ $report->user->email }}" class="truncate text-small text-primary hover:underline">{{ $report->user->email }}</a>
                @else
                    <p class="text-small text-text-muted">Akun pelapor sudah dihapus.</p>
                @endif
            </section>
        </div>
    </div>
</x-layouts.admin>
