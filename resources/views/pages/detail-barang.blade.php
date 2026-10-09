<x-layouts.app title="{{ $item->title }}" active="beranda">

    <section class="w-full px-20 py-14">
        <div class="mx-auto flex max-w-[1280px] flex-col gap-6">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-small text-text-secondary" aria-label="Breadcrumb">
                <a href="{{ route('beranda') }}" class="hover:text-primary">Beranda</a>
                <span aria-hidden="true">›</span>
                <span>{{ ucfirst($item->kategori) }}</span>
                <span aria-hidden="true">›</span>
                <span class="font-semibold text-primary">{{ $item->title }}</span>
            </nav>

            <div class="flex gap-8 rounded-[20px] border border-border bg-surface-white p-8 shadow-card">

                {{-- Kolom foto --}}
                <div class="flex flex-1 flex-col gap-4">
                    <div class="h-[400px] w-full overflow-hidden rounded-lg bg-surface-muted">
                        @if ($item->image_url)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="h-full w-full object-cover" />
                        @endif
                    </div>
                    @if (!empty($item->thumbnails))
                        <div class="flex gap-3">
                            @foreach ($item->thumbnails as $i => $thumb)
                                <button type="button" class="h-[60px] w-20 overflow-hidden rounded-sm border-2 {{ $i === 0 ? 'border-primary' : 'border-border' }}">
                                    <img src="{{ $thumb }}" alt="" class="h-full w-full object-cover" />
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Kolom info --}}
                <div class="flex flex-1 flex-col gap-6">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-2">
                            <x-status-badge :status="$item->status" />
                            <span class="rounded-md bg-[#f5f3ff] px-2.5 py-1 text-caption font-semibold uppercase text-primary">{{ $item->kategori }}</span>
                        </div>
                        <h1 class="text-[28px] font-extrabold leading-tight text-text">{{ $item->title }}</h1>
                    </div>

                    <hr class="border-border" />

                    <div class="flex flex-col gap-4">
                        <div class="flex gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-surface" aria-hidden="true">📍</div>
                            <div>
                                <p class="text-caption text-text-secondary">Lokasi Ditemukan</p>
                                <p class="text-body font-semibold text-text">{{ $item->location }}</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-surface" aria-hidden="true">🕒</div>
                            <div>
                                <p class="text-caption text-text-secondary">Tanggal Ditemukan</p>
                                <p class="text-body font-semibold text-text">{{ $item->found_at->translatedFormat('d F Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-surface" aria-hidden="true">👤</div>
                            <div>
                                <p class="text-caption text-text-secondary">Dilaporkan Oleh</p>
                                <p class="text-body font-semibold text-text">{{ $item->reporter_name }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-border" />

                    <div class="flex flex-col gap-2">
                        <p class="text-label font-semibold text-text">Deskripsi / Ciri-ciri:</p>
                        <p class="text-label leading-relaxed text-text-secondary">{{ $item->description }}</p>
                    </div>

                    {{-- CTA: state tersedia vs sudah diklaim --}}
                    <div class="flex flex-col gap-3">
                        @if ($item->status === 'diklaim')
                            <x-button variant="secondary" disabled class="w-full justify-center opacity-60">
                                Barang Sudah Diklaim
                            </x-button>
                        @else
                            <x-button variant="primary" href="{{ route('klaim.create', $item->id) }}" class="w-full justify-center">
                                Klaim Barang Ini
                            </x-button>
                        @endif
                        <x-button variant="secondary" href="#hubungi-pelapor" class="w-full justify-center">
                            Hubungi Pelapor
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>

{{--
    Controller (contoh):
    Route::get('/barang/{id}', function ($id) {
        $item = \App\Models\Barang::findOrFail($id);
        return view('pages.detail-barang', ['item' => $item]);
    })->name('barang.detail');
--}}
