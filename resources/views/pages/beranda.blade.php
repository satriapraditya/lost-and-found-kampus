<x-layouts.app title="Beranda" active="beranda">

    {{-- Hero + Search + Filter --}}
    <section class="w-full border-b border-border bg-surface-white px-20 pb-10 pt-14">
        <div class="mx-auto flex max-w-[1280px] flex-col gap-6">

            <div class="flex max-w-[720px] flex-col gap-3">
                <h1 class="text-hero font-extrabold leading-tight text-text">
                    Temukan Kembali Barang Anda yang Hilang
                </h1>

                <p class="text-body leading-relaxed text-text-secondary">
                    Layanan Lost &amp; Found digital untuk melacak, melaporkan,
                    dan mengklaim barang temuan di seluruh area kampus secara
                    aman dan terverifikasi.
                </p>
            </div>

            {{-- Form pencarian --}}
            <form
                method="GET"
                action="{{ route('beranda') }}"
                class="flex h-14 w-full max-w-[800px] items-center gap-3 rounded-lg border border-border bg-surface px-5"
            >
                <input
                    type="hidden"
                    name="kategori"
                    value="{{ request('kategori', 'semua') }}"
                />

                <span aria-hidden="true" class="text-text-muted">🔍</span>

                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder='Cari barang (contoh: "Kunci", "iPhone", "Dompet")...'
                    aria-label="Cari barang"
                    class="flex-1 bg-transparent text-body text-text placeholder:text-text-muted focus:outline-none"
                />

                <x-button type="submit" variant="primary">
                    Cari
                </x-button>
            </form>

            {{-- Filter kategori --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-label font-semibold text-text-secondary">
                    Kategori:
                </span>

                @php
                    $activeCategory = (string) request('kategori', 'semua');
                    $searchQuery = request('q');
                @endphp

                <a
                    href="{{ route('beranda', array_filter([
                        'kategori' => 'semua',
                        'q' => $searchQuery,
                    ], fn ($value) => $value !== null && $value !== '')) }}"
                    @if ($activeCategory === 'semua')
                        aria-current="page"
                    @endif
                    class="rounded-full px-4 py-2 text-small font-semibold {{ $activeCategory === 'semua' ? 'bg-primary text-white' : 'border border-border bg-white text-text-secondary hover:bg-surface-muted' }}"
                >
                    Semua
                </a>

                @foreach (($categories ?? collect()) as $category)
                    @php
                        $categoryName = (string) $category->name;
                        $isActive = $activeCategory === $categoryName;
                    @endphp

                    <a
                        href="{{ route('beranda', array_filter([
                            'kategori' => $categoryName,
                            'q' => $searchQuery,
                        ], fn ($value) => $value !== null && $value !== '')) }}"
                        @if ($isActive)
                            aria-current="page"
                        @endif
                        class="rounded-full px-4 py-2 text-small font-semibold {{ $isActive ? 'bg-primary text-white' : 'border border-border bg-white text-text-secondary hover:bg-surface-muted' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

        </div>
    </section>

    {{-- Hasil pencarian --}}
    <section class="w-full px-20 py-12">
        <div class="mx-auto flex max-w-[1280px] flex-col gap-8">

            <div class="flex items-center justify-between gap-4">
                <h2 class="text-h2 font-bold text-text">
                    Semua Laporan Temuan Terbaru
                </h2>

                <div class="flex items-center gap-2 text-label text-text-secondary">
                    <span>Urutkan:</span>
                    <span class="font-semibold text-primary">
                        Terbaru
                    </span>
                </div>
            </div>

            @php
                $displayItems = $items ?? collect();
            @endphp

            @forelse ($displayItems->chunk(3) as $row)
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

                    @foreach ($row as $item)
                        <x-card
                            title="{{ $item->title }}"
                            location="{{ $item->location }}"
                            timestamp="{{ $item->found_at ? $item->found_at->diffForHumans() : '-' }}"
                            status="{{ $item->status }}"
                            :imageUrl="$item->image_url"
                            :href="route('barang.detail', $item->id)"
                            :variant="$item->status === 'diklaim' ? 'unavailable' : 'normal'"
                        />
                    @endforeach

                </div>
            @empty
                {{-- Tampilan ketika tidak ada hasil --}}
                <div class="flex flex-col items-center gap-3 rounded-lg border border-dashed border-border py-16 text-center">
                    <span class="text-3xl" aria-hidden="true">🔎</span>

                    <p class="text-label font-semibold text-text">
                        Belum ada barang temuan yang cocok
                    </p>

                    <p class="text-small text-text-secondary">
                        Coba ubah kata kunci atau kategori pencarianmu.
                    </p>

                    <x-button
                        variant="secondary"
                        href="{{ route('beranda') }}"
                    >
                        Reset Pencarian
                    </x-button>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if (is_callable([$displayItems, 'links']))
                <nav
                    class="flex items-center justify-center gap-2"
                    aria-label="Pagination"
                >
                    {{ $displayItems->appends(request()->query())->links() }}
                </nav>
            @endif

        </div>
    </section>

</x-layouts.app>
