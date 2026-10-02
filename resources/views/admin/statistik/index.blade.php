@php
    use App\Services\ReportStatistics;

    $fieldClass = 'h-11 w-full rounded-sm border border-border bg-surface-white px-3 text-small text-text focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp

<x-layouts.admin title="Statistik & Laporan" subtitle="Analisis laporan barang hilang dan temuan, lalu unduh sebagai Excel atau PDF.">
    <x-slot:actions>
        <x-button variant="secondary" :href="route('admin.statistics.excel', $filters)">
            <x-admin.icon name="download" class="h-4 w-4" />
            Export Excel
        </x-button>
        <x-button
            variant="primary"
            :href="route('admin.statistics.pdf', $filters)"
            target="_blank"
            title="Membuka versi cetak, lalu pilih Simpan sebagai PDF di dialog print"
        >
            <x-admin.icon name="document" class="h-4 w-4" />
            Export PDF
        </x-button>
    </x-slot:actions>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.statistics') }}" class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <label class="flex flex-col gap-1.5">
                <span class="text-caption font-semibold uppercase tracking-wide text-text-muted">Dari tanggal</span>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="{{ $fieldClass }}">
            </label>
            <label class="flex flex-col gap-1.5">
                <span class="text-caption font-semibold uppercase tracking-wide text-text-muted">Sampai tanggal</span>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="{{ $fieldClass }}">
            </label>
            <label class="flex flex-col gap-1.5">
                <span class="text-caption font-semibold uppercase tracking-wide text-text-muted">Jenis</span>
                <select name="type" class="{{ $fieldClass }}">
                    <option value="">Semua jenis</option>
                    @foreach (ReportStatistics::TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['type'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex flex-col gap-1.5">
                <span class="text-caption font-semibold uppercase tracking-wide text-text-muted">Kategori</span>
                <select name="category_id" class="{{ $fieldClass }}">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="flex flex-col gap-1.5">
                <span class="text-caption font-semibold uppercase tracking-wide text-text-muted">Status</span>
                <select name="status" class="{{ $fieldClass }}">
                    <option value="">Semua status</option>
                    @foreach (ReportStatistics::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        @if ($errors->any())
            <p class="text-small text-danger-text">{{ $errors->first() }}</p>
        @endif

        <div class="flex flex-wrap items-center justify-end gap-2">
            <x-button variant="secondary" :href="route('admin.statistics')">Reset</x-button>
            <x-button type="submit" variant="primary">Terapkan Filter</x-button>
        </div>
    </form>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-admin.stat-card label="Total Laporan" :value="$summary['total']" icon="document" />
        <x-admin.stat-card label="Barang Hilang" :value="$summary['lost']" icon="search" />
        <x-admin.stat-card label="Barang Temuan" :value="$summary['found']" icon="box" />
        <x-admin.stat-card label="Selesai" :value="$summary['returned']" icon="check" hint="Sudah kembali ke pemilik" />
        <x-admin.stat-card label="Tingkat Penyelesaian" :value="$summary['return_rate'] . '%'" icon="shield" hint="Dari seluruh laporan" />
    </div>

    {{-- Grafik --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6 xl:col-span-2">
            <div>
                <h2 class="text-card-title font-bold text-text">Tren Laporan</h2>
                <p class="text-caption text-text-muted">Jumlah laporan masuk per bulan sesuai filter</p>
            </div>
            <x-admin.chart
                type="line"
                :labels="$trend['labels']"
                :series="[
                    ['label' => 'Barang Hilang', 'data' => $trend['lost'], 'color' => 'lost'],
                    ['label' => 'Barang Temuan', 'data' => $trend['found'], 'color' => 'found'],
                    ['label' => 'Selesai', 'data' => $trend['completed'], 'color' => 'completed'],
                ]"
                label="Grafik garis tren laporan hilang, temuan, dan selesai per bulan"
            />
        </section>

        <section class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
            <div>
                <h2 class="text-card-title font-bold text-text">Laporan per Hari</h2>
                <p class="text-caption text-text-muted">Hari paling ramai laporan masuk</p>
            </div>
            <x-admin.chart
                type="bar"
                :labels="$weekdays['labels']"
                :series="[['label' => 'Laporan', 'data' => $weekdays['data'], 'color' => 'found']]"
                label="Grafik batang jumlah laporan per hari dalam seminggu"
            />
        </section>
    </div>

    {{-- Tabel data --}}
    <section class="flex flex-col gap-4 rounded-lg border border-border bg-surface-white p-5 shadow-card md:p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-card-title font-bold text-text">Data Laporan</h2>
                <p class="text-caption text-text-muted">Data yang sama dengan isi file export</p>
            </div>
        </div>

        @if ($table->isEmpty())
            <div class="flex items-center justify-center rounded-md border border-dashed border-border py-12 text-small text-text-muted">
                Tidak ada laporan yang cocok dengan filter.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-small">
                    <thead>
                        <tr class="border-b border-border text-caption uppercase tracking-wide text-text-muted">
                            <th class="py-2.5 pr-4 font-semibold">Barang</th>
                            <th class="py-2.5 pr-4 font-semibold">Jenis</th>
                            <th class="py-2.5 pr-4 font-semibold">Lokasi</th>
                            <th class="py-2.5 pr-4 font-semibold">Pelapor</th>
                            <th class="py-2.5 pr-4 font-semibold">Tanggal Masuk</th>
                            <th class="py-2.5 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($table as $report)
                            <tr class="border-b border-surface-muted last:border-0">
                                <td class="py-3 pr-4">
                                    <p class="font-semibold text-text">{{ $report->item_name }}</p>
                                    <p class="text-caption text-text-muted">{{ $report->category->name ?? '—' }}</p>
                                </td>
                                <td class="py-3 pr-4 text-text-secondary">{{ ReportStatistics::TYPES[$report->type] ?? $report->type }}</td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $report->location->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $report->user->name ?? '—' }}</td>
                                <td class="py-3 pr-4 text-text-secondary">{{ $report->created_at->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="py-3"><x-status-badge :status="ReportStatistics::BADGE[$report->status] ?? 'menunggu'" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col items-center justify-between gap-3 text-small text-text-secondary sm:flex-row">
                <p>Menampilkan {{ $table->firstItem() }}–{{ $table->lastItem() }} dari {{ $table->total() }} laporan</p>
                <div class="flex items-center gap-2">
                    <x-button variant="secondary" :href="$table->previousPageUrl()" :disabled="$table->onFirstPage()">← Sebelumnya</x-button>
                    <x-button variant="secondary" :href="$table->nextPageUrl()" :disabled="! $table->hasMorePages()">Berikutnya →</x-button>
                </div>
            </div>
        @endif
    </section>
</x-layouts.admin>
