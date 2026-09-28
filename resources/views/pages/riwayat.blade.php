<x-layouts.app title="Riwayat Saya" active="riwayat">

    <section class="w-full px-4 py-10 md:px-20 md:py-14">
        <div class="mx-auto flex max-w-[1280px] flex-col gap-6">

            <div class="flex flex-col gap-1.5">
                <h1 class="text-[24px] font-extrabold text-text">Riwayat Saya</h1>
                <p class="text-label text-text-secondary">Pantau aktivitas lapor temuan dan klaim yang Anda ajukan.</p>
            </div>

            {{-- Tab: di mobile memilih panel yang tampil, di desktop kedua panel tetap tampil berdampingan --}}
            <div class="flex gap-2 border-b border-border" role="tablist">
                <button
                    type="button"
                    role="tab"
                    data-tab="laporan"
                    aria-selected="true"
                    class="-mb-px border-b-[3px] border-primary px-4 pb-3 text-[15px] font-bold text-primary"
                >
                    Laporan Saya ({{ $reports->count() }})
                </button>
                <button
                    type="button"
                    role="tab"
                    data-tab="klaim"
                    aria-selected="false"
                    class="-mb-px border-b-[3px] border-transparent px-4 pb-3 text-[15px] font-medium text-text-secondary"
                >
                    Klaim Saya ({{ $claims->count() }})
                </button>
            </div>

            <div class="grid grid-cols-1 items-start gap-6 md:grid-cols-2">

                {{-- Panel laporan --}}
                <div data-panel="laporan" class="flex flex-col gap-4 rounded-lg border border-border bg-white p-5 shadow-card md:flex md:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-card-title font-bold text-text">Laporan Temuan Saya</h2>
                        <span class="text-caption text-text-muted">Terakhir diperbarui hari ini</span>
                    </div>
                    <hr class="border-border" />

                    <div class="flex flex-col gap-3">
                        @forelse ($reports as $report)
                            <a
                                href="{{ route('barang.detail', $report->id) }}"
                                class="flex items-center justify-between gap-3 rounded-md border border-surface-muted p-4 transition-colors hover:bg-surface"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-sm bg-surface" aria-hidden="true">🔍</div>
                                    <div class="min-w-0">
                                        <p class="truncate text-label font-bold text-text">{{ $report->title }}</p>
                                        <p class="text-caption text-text-muted">Diajukan pada: {{ $report->submitted_at->locale('id')->translatedFormat('d M Y') }}</p>
                                    </div>
                                </div>
                                <x-status-badge :status="$report->status" />
                            </a>
                        @empty
                            {{-- State: belum ada laporan --}}
                            <div class="flex flex-col items-center gap-3 rounded-md border border-dashed border-border py-10 text-center">
                                <p class="text-label font-semibold text-text">Belum ada laporan temuan</p>
                                <p class="text-small text-text-secondary">Laporan yang kamu kirim akan muncul di sini.</p>
                                <x-button variant="primary" href="{{ route('lapor.create') }}">Laporkan Barang Temuan</x-button>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Panel klaim --}}
                <div data-panel="klaim" class="hidden flex flex-col gap-4 rounded-lg border border-border bg-white p-5 shadow-card md:flex md:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-card-title font-bold text-text">Permohonan Klaim Saya</h2>
                        <span class="text-caption text-text-muted">Dalam proses verifikasi</span>
                    </div>
                    <hr class="border-border" />

                    <div class="flex flex-col gap-3">
                        @forelse ($claims as $claim)
                            <a
                                href="{{ route('barang.detail', $claim->item_id) }}"
                                class="flex items-center justify-between gap-3 rounded-md border border-surface-muted p-4 transition-colors hover:bg-surface"
                            >
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-sm bg-surface" aria-hidden="true">📝</div>
                                    <div class="min-w-0">
                                        <p class="truncate text-label font-bold text-text">{{ $claim->title }}</p>
                                        <p class="text-caption text-text-muted">Diajukan pada: {{ $claim->submitted_at->locale('id')->translatedFormat('d M Y') }}</p>
                                    </div>
                                </div>
                                <x-status-badge :status="$claim->status" />
                            </a>
                        @empty
                            {{-- State: belum ada klaim --}}
                            <div class="flex flex-col items-center gap-3 rounded-md border border-dashed border-border py-10 text-center">
                                <p class="text-label font-semibold text-text">Belum ada klaim</p>
                                <p class="text-small text-text-secondary">Cari barangmu di Beranda, lalu ajukan klaim.</p>
                                <x-button variant="secondary" href="{{ route('beranda') }}">Cari di Beranda</x-button>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        (function () {
            var tabs = document.querySelectorAll('[data-tab]');
            var panels = document.querySelectorAll('[data-panel]');
            var on = ['border-primary', 'font-bold', 'text-primary'];
            var off = ['border-transparent', 'font-medium', 'text-text-secondary'];

            function show(name) {
                tabs.forEach(function (tab) {
                    var active = tab.dataset.tab === name;
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    on.forEach(function (c) { tab.classList.toggle(c, active); });
                    off.forEach(function (c) { tab.classList.toggle(c, !active); });
                });
                panels.forEach(function (panel) {
                    panel.classList.toggle('hidden', panel.dataset.panel !== name);
                });
            }

            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () { show(tab.dataset.tab); });
            });
        })();
    </script>

</x-layouts.app>
