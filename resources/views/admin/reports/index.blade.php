@php
    use App\Services\ReportStatistics;

    $isLost = $type === 'lost';
    $route = $isLost ? 'admin.lost' : 'admin.found';
    $fieldClass = 'h-11 w-full rounded-sm border border-border bg-surface-white px-3 text-small text-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp

<x-layouts.admin
    :title="ReportStatistics::TYPES[$type]"
    :subtitle="$isLost
        ? 'Tinjau laporan barang yang hilang sebelum tampil di situs.'
        : 'Tinjau laporan barang temuan dan pantau yang sudah kembali ke pemilik.'"
>
    <section class="flex flex-col gap-5 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
        <x-admin.status-tabs
            :route="$route . '.index'"
            :counts="$statusCounts"
            :current="$filters['status'] ?? null"
            :query="['q' => $filters['q'] ?? null, 'category_id' => $filters['category_id'] ?? null]"
        />

        <form method="GET" action="{{ route($route . '.index') }}" class="flex flex-col gap-3 sm:flex-row">
            @isset($filters['status'])
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endisset
            <input
                type="search"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Cari nama barang…"
                aria-label="Cari nama barang"
                class="{{ $fieldClass }} sm:flex-1"
            >
            <select name="category_id" aria-label="Kategori" class="{{ $fieldClass }} sm:w-56">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-button type="submit" variant="primary">Cari</x-button>
        </form>

        @if ($reports->isEmpty())
            <div class="flex items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                Tidak ada laporan yang cocok.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-small">
                    <thead>
                        <tr class="border-b border-border text-caption uppercase tracking-wide text-text-muted">
                            <th class="py-2.5 pr-4 font-semibold">Barang</th>
                            <th class="py-2.5 pr-4 font-semibold">Lokasi</th>
                            <th class="py-2.5 pr-4 font-semibold">Pelapor</th>
                            <th class="py-2.5 pr-4 font-semibold">{{ $isLost ? 'Tanggal Hilang' : 'Tanggal Ditemukan' }}</th>
                            <th class="py-2.5 pr-4 font-semibold">Status</th>
                            <th class="py-2.5 font-semibold"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr class="border-b border-surface-muted last:border-0">
                                <td class="py-3 pr-4">
                                    <a href="{{ route($route . '.show', $report) }}" class="font-semibold text-text hover:text-primary">{{ $report->item_name }}</a>
                                    <p class="text-caption text-text-muted">{{ $report->category->name ?? '—' }}</p>
                                </td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $report->location->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $report->user->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $report->event_date?->locale('id')->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="py-3 pr-4"><x-status-badge :status="ReportStatistics::BADGE[$report->status] ?? 'menunggu'" /></td>
                                <td class="py-3 text-right">
                                    <x-button variant="ghost" :href="route($route . '.show', $report)">
                                        {{ $report->status === 'pending' ? 'Tinjau' : 'Detail' }}
                                    </x-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-admin.pagination :paginator="$reports" noun="laporan" />
        @endif
    </section>
</x-layouts.admin>
