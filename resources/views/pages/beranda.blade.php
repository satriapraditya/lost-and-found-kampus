<x-layouts.app title="Beranda" active="beranda">

    {{-- Hero + Search + Filter --}}
    <section class="w-full border-b border-border bg-surface-white px-4 pb-8 pt-8 sm:px-8 sm:pt-10 lg:px-20 lg:pb-10 lg:pt-14">
        <div class="mx-auto flex max-w-[1280px] flex-col gap-6">

            <div class="flex max-w-[720px] flex-col gap-3">
                <h1 class="text-[28px] font-extrabold sm:text-hero leading-tight text-text">
                    Temukan Kembali Barang Anda yang Hilang
                </h1>

                <p class="text-body leading-relaxed text-text-secondary">
                    Campus Find adalah layanan lost &amp; found digital untuk seluruh
                    civitas akademika. Kehilangan barang di kampus? Cari di daftar
                    barang temuan, atau laporkan barang yang Anda temukan lengkap
                    dengan foto dan lokasinya. Setiap laporan ditinjau admin terlebih
                    dahulu, dan barang hanya diserahkan kepada pemilik yang berhasil
                    membuktikan kepemilikannya, sehingga prosesnya aman dan terverifikasi.
                </p>
            </div>

            {{-- Form pencarian --}}
            <form
                method="GET"
                action="{{ route('beranda') }}"
                class="flex h-14 w-full max-w-[800px] items-center gap-2 rounded-lg border border-border bg-surface pl-4 pr-2 sm:gap-3 sm:px-5"
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
                    class="min-w-0 flex-1 bg-transparent text-body text-text placeholder:text-text-muted focus:outline-none"
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
                    class="rounded-full px-4 py-2 text-small font-semibold {{ $activeCategory === 'semua' ? 'bg-primary text-white' : 'border border-border bg-surface-white text-text-secondary hover:bg-surface-muted' }}"
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
                        class="rounded-full px-4 py-2 text-small font-semibold {{ $isActive ? 'bg-primary text-white' : 'border border-border bg-surface-white text-text-secondary hover:bg-surface-muted' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

        </div>
    </section>

    {{-- Hasil pencarian --}}
    <section class="w-full px-4 py-8 sm:px-8 sm:py-12 lg:px-20">
        <div class="mx-auto flex max-w-[1280px] flex-col gap-8">

            <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
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

    {{-- Cara kerja sistem --}}
    @php
        $steps = [
            ['title' => 'Lapor barang', 'text' => 'Unggah foto dan informasi barang temuan, lengkap dengan lokasi dan tanggal penemuan.'],
            ['title' => 'Verifikasi admin', 'text' => 'Admin memeriksa laporan. Yang sesuai ditampilkan di beranda, yang kurang jelas dikembalikan untuk diperbaiki.'],
            ['title' => 'Cari dan identifikasi', 'text' => 'Siapa saja bisa mencari barang lewat kata kunci dan kategori, lalu melihat detailnya.'],
            ['title' => 'Klaim dan serah terima', 'text' => 'Pemilik mengisi ciri khusus barang untuk diverifikasi, lalu barang diserahkan secara langsung.'],
        ];
    @endphp

    <section class="w-full px-4 pb-8 sm:px-8 sm:pb-12 lg:px-20" aria-labelledby="cara-kerja">
        <div class="mx-auto flex max-w-[1280px] flex-col gap-6">
            <h2 id="cara-kerja" class="text-center text-h2 font-bold text-text">
                Cara kerja sistem
            </h2>

            <ol class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $i => $step)
                    <li class="flex flex-col gap-3 rounded-lg border border-border bg-surface-white p-6">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-label font-bold text-white" aria-hidden="true">
                            {{ $i + 1 }}
                        </span>
                        <h3 class="text-card-title font-bold text-text">
                            <span class="sr-only">Langkah {{ $i + 1 }}: </span>{{ $step['title'] }}
                        </h3>
                        <p class="text-small leading-relaxed text-text-secondary">
                            {{ $step['text'] }}
                        </p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

</x-layouts.app>
