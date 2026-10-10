@php
    use App\Http\Controllers\Admin\ClaimController;
    use App\Services\ReportStatistics;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $report = $claim->report;
    $image = $report->images->sortByDesc('is_primary')->first();
    $imageUrl = $image
        ? (Str::startsWith($image->image_path, ['http://', 'https://']) ? $image->image_path : Storage::disk('public')->url($image->image_path))
        : null;
    $reportRoute = $report->type === 'lost' ? 'admin.lost.show' : 'admin.found.show';
    $card = 'flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6';
    $textareaClass = 'w-full resize-none rounded-sm border border-border bg-surface-white px-3 py-2.5 text-small text-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
    $phone = $claim->whatsapp ?: $claim->user?->phone; // nomor di form klaim, atau nomor di akun
    $whatsapp = $phone ? preg_replace('/^0/', '62', preg_replace('/\D/', '', $phone)) : null;
@endphp

<x-layouts.admin title="Verifikasi Klaim" :subtitle="'Klaim untuk ' . $report->item_name . ' · diajukan ' . $claim->created_at->locale('id')->diffForHumans()">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('admin.claims.index')">
            <x-admin.icon name="arrow-left" class="h-4 w-4" />
            Kembali
        </x-button>
    </x-slot:actions>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="flex flex-col gap-6 xl:col-span-2">
            {{-- Bandingkan: pengakuan pengklaim vs data laporan --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section class="{{ $card }}">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-card-title font-bold text-text">Pengakuan Pengklaim</h2>
                        <x-status-badge :status="ClaimController::BADGE[$claim->status] ?? 'menunggu'" />
                    </div>
                    <dl class="flex flex-col gap-4 text-small">
                        <div>
                            <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Deskripsi barang</dt>
                            <dd class="mt-1 whitespace-pre-line text-text">{{ $claim->claim_description }}</dd>
                        </div>
                        <div>
                            <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Bukti kepemilikan</dt>
                            <dd class="mt-1 whitespace-pre-line text-text">{{ $claim->proof_description }}</dd>
                        </div>
                        @if ($claim->admin_note)
                            <div class="rounded-md bg-surface-muted px-4 py-3">
                                <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Catatan admin</dt>
                                <dd class="mt-1 text-text">{{ $claim->admin_note }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>

                <section class="{{ $card }}">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="text-card-title font-bold text-text">Data Laporan</h2>
                        <x-button variant="ghost" :href="route($reportRoute, $report)" class="!px-2 !py-1">Buka →</x-button>
                    </div>
                    @if ($imageUrl)
                        <img src="{{ $imageUrl }}" alt="Foto {{ $report->item_name }}" class="aspect-video w-full rounded-md border border-border object-cover">
                    @endif
                    <dl class="flex flex-col gap-4 text-small">
                        <div>
                            <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Barang</dt>
                            <dd class="mt-1 text-text">{{ $report->item_name }} · {{ $report->category->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Ditemukan</dt>
                            <dd class="mt-1 text-text">{{ $report->location->name ?? '—' }}, {{ $report->event_date?->locale('id')->translatedFormat('d M Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Deskripsi</dt>
                            <dd class="mt-1 whitespace-pre-line text-text">{{ $report->description }}</dd>
                        </div>
                        @if ($report->special_features)
                            <div>
                                <dt class="text-caption font-semibold uppercase tracking-wide text-text-muted">Ciri khusus (rahasia)</dt>
                                <dd class="mt-1 whitespace-pre-line text-text">{{ $report->special_features }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            </div>

            @if ($otherClaims->isNotEmpty())
                <section class="{{ $card }}">
                    <div>
                        <h2 class="text-card-title font-bold text-text">Klaim Lain untuk Barang Ini</h2>
                        <p class="text-caption text-text-muted">Menyetujui klaim ini otomatis menolak klaim lain yang masih menunggu.</p>
                    </div>
                    @foreach ($otherClaims as $other)
                        <a href="{{ route('admin.claims.show', $other) }}" class="flex items-center justify-between gap-3 rounded-md border border-border px-4 py-3 text-small hover:bg-surface-muted">
                            <span>
                                <span class="font-semibold text-text">{{ $other->user->name ?? '—' }}</span>
                                <span class="text-text-muted">· {{ $other->created_at->locale('id')->diffForHumans() }}</span>
                            </span>
                            <x-status-badge :status="ClaimController::BADGE[$other->status] ?? 'menunggu'" />
                        </a>
                    @endforeach
                </section>
            @endif

            @if ($claim->histories->isNotEmpty())
                <section class="{{ $card }}">
                    <h2 class="text-card-title font-bold text-text">Riwayat</h2>
                    <ol class="flex flex-col gap-3 border-l-2 border-border pl-4">
                        @foreach ($claim->histories as $history)
                            <li class="text-small">
                                <p class="font-semibold text-text">{{ ClaimController::STATUSES[$history->status] ?? $history->status }}</p>
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

                {{-- Semua tombol diproses AdminController@updateClaim (status + note) --}}
                @if ($claim->status === 'pending')
                    @if ($report->status === 'approved')
                        <details class="rounded-md border border-border" open>
                            <summary class="cursor-pointer list-none px-4 py-2.5 text-center text-label font-semibold text-primary">Setujui Klaim</summary>
                            <form method="POST" action="{{ route('admin.claims.update', $claim) }}" class="flex flex-col gap-3 border-t border-border p-4">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <label for="approve_note" class="text-small font-semibold text-text-secondary">Catatan untuk pengklaim (opsional)</label>
                                <textarea id="approve_note" name="note" rows="2" class="{{ $textareaClass }}" placeholder="Contoh: Ambil di pos satpam Gedung A, jam 08.00–16.00."></textarea>
                                <x-button type="submit" variant="primary" class="w-full">Setujui</x-button>
                            </form>
                        </details>
                    @else
                        <p class="rounded-md bg-warning-bg px-4 py-3 text-small text-warning-text">
                            Klaim belum bisa disetujui karena status laporan barang ini <strong>{{ ReportStatistics::STATUSES[$report->status] ?? $report->status }}</strong>, bukan Disetujui.
                        </p>
                    @endif

                    <details class="rounded-md border border-border" @if ($errors->has('note')) open @endif>
                        <summary class="cursor-pointer list-none px-4 py-2.5 text-center text-label font-semibold text-danger-text">Tolak Klaim…</summary>
                        <form method="POST" action="{{ route('admin.claims.update', $claim) }}" class="flex flex-col gap-3 border-t border-border p-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <label for="reject_note" class="text-small font-semibold text-text-secondary">Alasan penolakan</label>
                            <textarea id="reject_note" name="note" rows="3" required minlength="5" class="{{ $textareaClass }}" placeholder="Contoh: Ciri-ciri yang disebutkan tidak cocok dengan barang.">{{ old('note') }}</textarea>
                            @error('note')
                                <p class="text-small text-danger-text">{{ $message }}</p>
                            @enderror
                            <x-button type="submit" variant="danger" class="w-full">Kirim Penolakan</x-button>
                        </form>
                    </details>
                @elseif ($claim->status === 'approved')
                    <p class="text-small text-text-secondary">Klaim disetujui. Setelah barang diserahkan langsung ke pengklaim, tandai selesai.</p>
                    <form method="POST" action="{{ route('admin.claims.update', $claim) }}" onsubmit="return confirm('Tandai barang sudah diserahkan ke pengklaim?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <x-button type="submit" variant="primary" class="w-full">Barang Sudah Diserahkan</x-button>
                    </form>
                @elseif ($claim->status === 'completed')
                    <p class="text-small text-text-secondary">Barang sudah diserahkan ke pemiliknya.</p>
                @else
                    <p class="text-small text-text-secondary">Klaim ini ditolak.</p>
                @endif
            </section>

            {{-- Pengklaim --}}
            <section class="{{ $card }}">
                <h2 class="text-card-title font-bold text-text">Pengklaim</h2>
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-small font-bold text-primary">
                        {{ strtoupper(mb_substr($claim->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0 text-small">
                        <p class="truncate font-semibold text-text">{{ $claim->user->name ?? '—' }}</p>
                        <p class="truncate text-text-muted">{{ $claim->user->nim ?? '' }} · {{ $claim->user->study_program ?? '' }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-1.5 text-small">
                    @if ($claim->user?->email)
                        <a href="mailto:{{ $claim->user->email }}" class="truncate text-primary hover:underline">{{ $claim->user->email }}</a>
                    @endif
                    @if ($whatsapp)
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="text-primary hover:underline">WhatsApp {{ $phone }}</a>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.admin>
