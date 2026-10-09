@php
    use App\Http\Controllers\Admin\ClaimController;

    $fieldClass = 'h-11 w-full rounded-sm border border-border bg-surface-white px-3 text-small text-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp

<x-layouts.admin title="Verifikasi Klaim" subtitle="Cocokkan bukti kepemilikan dengan laporan barang temuan sebelum barang diserahkan.">
    <section class="flex flex-col gap-5 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
        <x-admin.status-tabs
            route="admin.claims.index"
            :counts="$statusCounts"
            :current="$filters['status'] ?? null"
            :query="['q' => $filters['q'] ?? null]"
        />

        <form method="GET" action="{{ route('admin.claims.index') }}" class="flex flex-col gap-3 sm:flex-row">
            @isset($filters['status'])
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endisset
            <input
                type="search"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Cari nama barang atau pengklaim…"
                aria-label="Cari nama barang atau pengklaim"
                class="{{ $fieldClass }} sm:flex-1"
            >
            <x-button type="submit" variant="primary">Cari</x-button>
        </form>

        @if ($claims->isEmpty())
            <div class="flex items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                Tidak ada klaim yang cocok.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-small">
                    <thead>
                        <tr class="border-b border-border text-caption uppercase tracking-wide text-text-muted">
                            <th class="py-2.5 pr-4 font-semibold">Barang</th>
                            <th class="py-2.5 pr-4 font-semibold">Pengklaim</th>
                            <th class="py-2.5 pr-4 font-semibold">Diajukan</th>
                            <th class="py-2.5 pr-4 font-semibold">Status</th>
                            <th class="py-2.5 font-semibold"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($claims as $claim)
                            <tr class="border-b border-surface-muted last:border-0">
                                <td class="py-3 pr-4">
                                    <a href="{{ route('admin.claims.show', $claim) }}" class="font-semibold text-text hover:text-primary">{{ $claim->report->item_name ?? '—' }}</a>
                                    <p class="text-caption text-text-muted">{{ $claim->report->category->name ?? '—' }}</p>
                                </td>
                                <td class="py-3 pr-4">
                                    <p class="text-text">{{ $claim->user->name ?? '—' }}</p>
                                    <p class="text-caption text-text-muted">{{ $claim->user->nim ?? '' }}</p>
                                </td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $claim->created_at->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="py-3 pr-4"><x-status-badge :status="ClaimController::BADGE[$claim->status] ?? 'menunggu'" /></td>
                                <td class="py-3 text-right">
                                    <x-button variant="ghost" :href="route('admin.claims.show', $claim)">
                                        {{ $claim->status === 'pending' ? 'Verifikasi' : 'Detail' }}
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin.pagination :paginator="$claims" noun="klaim" />
        @endif
    </section>
</x-layouts.admin>
