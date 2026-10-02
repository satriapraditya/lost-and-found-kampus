@php
    use App\Services\ReportStatistics;
@endphp

<x-layouts.admin
    title="Dashboard"
    :subtitle="'Selamat datang' . (auth()->check() ? ', ' . auth()->user()->name : '') . '. Ini ringkasan Lost & Found kampus hari ini.'"
>
    {{-- Perlu tindakan --}}
    @if ($summary['pending_reports'] > 0 || $summary['pending_claims'] > 0)
        <div class="flex flex-col gap-1 rounded-md border border-warning-text/20 bg-warning-bg px-5 py-4 text-small text-warning-text sm:flex-row sm:items-center sm:gap-3">
            <x-admin.icon name="clock" />
            <p>
                <span class="font-semibold">Perlu ditinjau:</span>
                {{ $summary['pending_reports'] }} laporan baru dan {{ $summary['pending_claims'] }} klaim menunggu verifikasi.
            </p>
        </div>
    @endif

    {{-- Kartu ringkasan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card
            label="Barang Hilang"
            :value="$summary['lost']"
            icon="search"
            :hint="$summary['lost_this_month'] . ' dilaporkan bulan ini'"
        />
        <x-admin.stat-card
            label="Barang Temuan"
            :value="$summary['found']"
            icon="box"
            :hint="$summary['found_this_month'] . ' dilaporkan bulan ini'"
        />
        <x-admin.stat-card
            label="Klaim Menunggu"
            :value="$summary['pending_claims']"
            icon="check"
            hint="Perlu diverifikasi admin"
        />
        <x-admin.stat-card
            label="Sudah Dikembalikan"
            :value="$summary['returned']"
            icon="shield"
            :hint="$summary['return_rate'] . '% dari semua laporan sudah selesai'"
        />
    </div>

    {{-- Tren & kategori --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6 xl:col-span-2">
            <div>
                <h2 class="text-card-title font-bold text-text">Tren Bulanan</h2>
                <p class="text-caption text-text-muted">Laporan masuk 8 bulan terakhir</p>
            </div>
            <x-admin.chart
                type="bar"
                :labels="$trend['labels']"
                :series="[
                    ['label' => 'Barang Hilang', 'data' => $trend['lost'], 'color' => 'lost'],
                    ['label' => 'Barang Temuan', 'data' => $trend['found'], 'color' => 'found'],
                ]"
                label="Grafik batang jumlah laporan barang hilang dan temuan per bulan"
            />
        </section>

        <section class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
            <div>
                <h2 class="text-card-title font-bold text-text">Sebaran Kategori</h2>
                <p class="text-caption text-text-muted">Semua laporan berdasarkan kategori</p>
            </div>

            @if ($categories->isEmpty())
                <div class="flex flex-1 items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                    Belum ada data laporan.
                </div>
            @else
                <div class="relative">
                    <x-admin.chart
                        type="doughnut"
                        :labels="$categories->pluck('label')->all()"
                        :series="[['label' => 'Laporan', 'data' => $categories->pluck('total')->all()]]"
                        :height="200"
                        label="Diagram donat sebaran laporan per kategori"
                    />
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[28px] font-extrabold leading-none text-text">{{ $categories->sum('total') }}</span>
                        <span class="text-caption text-text-muted">laporan</span>
                    </div>
                </div>

                <ul class="flex flex-col gap-2">
                    @foreach ($categories as $i => $category)
                        <li class="flex items-center justify-between gap-3 text-small">
                            <span class="flex min-w-0 items-center gap-2 text-text-secondary">
                                <span
                                    class="h-2.5 w-2.5 shrink-0 rounded-full"
                                    data-category-swatch="{{ $i }}"
                                    @if ($category['label'] === 'Lainnya') data-other @endif
                                ></span>
                                <span class="truncate">{{ $category['label'] }}</span>
                            </span>
                            <span class="shrink-0 font-semibold text-text">
                                {{ $category['total'] }}
                                <span class="font-normal text-text-muted">({{ $category['percent'] }}%)</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    {{-- Lokasi & laporan terbaru --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
            <div>
                <h2 class="text-card-title font-bold text-text">Lokasi Terbanyak</h2>
                <p class="text-caption text-text-muted">Tempat paling sering ada laporan</p>
            </div>

            @forelse ($locations as $location)
                @php $width = $locations->max('total') > 0 ? $location['total'] / $locations->max('total') * 100 : 0; @endphp
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between gap-3 text-small">
                        <span class="truncate text-text-secondary">{{ $location['label'] }}</span>
                        <span class="shrink-0 font-semibold text-text">{{ $location['total'] }}</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-surface-muted">
                        <div class="h-1.5 rounded-full bg-primary" style="width: {{ $width }}%"></div>
                    </div>
                </div>
            @empty
                <div class="flex flex-1 items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                    Belum ada data lokasi.
                </div>
            @endforelse
        </section>

        <section class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6 xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-card-title font-bold text-text">Laporan Terbaru</h2>
                    <p class="text-caption text-text-muted">5 laporan yang terakhir masuk</p>
                </div>
                <x-button variant="ghost" :href="route('admin.statistics')">Lihat semua →</x-button>
            </div>

            @if ($latestReports->isEmpty())
                <div class="flex items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                    Belum ada laporan masuk.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] text-left text-small">
                        <thead>
                            <tr class="border-b border-border text-caption uppercase tracking-wide text-text-muted">
                                <th class="py-2.5 pr-4 font-semibold">Barang</th>
                                <th class="py-2.5 pr-4 font-semibold">Jenis</th>
                                <th class="py-2.5 pr-4 font-semibold">Lokasi</th>
                                <th class="py-2.5 pr-4 font-semibold">Masuk</th>
                                <th class="py-2.5 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestReports as $report)
                                <tr class="border-b border-surface-muted last:border-0">
                                    <td class="py-3 pr-4">
                                        <p class="font-semibold text-text">{{ $report->item_name }}</p>
                                        <p class="text-caption text-text-muted">{{ $report->category->name ?? '—' }}</p>
                                    </td>
                                    <td class="py-3 pr-4 text-text-secondary">{{ ReportStatistics::TYPES[$report->type] ?? $report->type }}</td>
                                    <td class="py-3 pr-4 text-text-secondary">{{ $report->location->name ?? '—' }}</td>
                                    <td class="py-3 pr-4 text-text-secondary">{{ $report->created_at->locale('id')->diffForHumans() }}</td>
                                    <td class="py-3"><x-status-badge :status="ReportStatistics::BADGE[$report->status] ?? 'menunggu'" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>
