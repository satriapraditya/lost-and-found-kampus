@php
    $links = [
        'beranda' => ['label' => 'Beranda', 'route' => 'beranda'],
        'lapor' => ['label' => 'Lapor Temuan', 'route' => 'lapor.create'],
        'riwayat' => ['label' => 'Riwayat Saya', 'route' => 'riwayat'],
    ];
@endphp

{{--
    Layar lebar (lg ke atas): tampilan sesuai Figma.
    Layar kecil: menu, tombol Admin, dan Keluar pindah ke panel ☰.
--}}
<header class="relative w-full border-b border-border bg-surface-white">
    <div class="mx-auto flex h-[72px] max-w-[1440px] items-center justify-between gap-4 px-4 sm:px-8 lg:px-20">
        <a href="{{ route('beranda') }}" class="flex shrink-0 items-center gap-2.5">
            <div class="flex h-20 w-20 items-center justify-center rounded-md  ">
                <img
    src="{{ asset('images/logo_lostandfound.png') }}"
    alt="Logo Campus Find"
    class="h-20 w-20 object-contain"
>
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-h3 font-extrabold text-text">Campus Find</span>
                <span class="text-[11px] font-medium tracking-wide text-primary">LOST &amp; FOUND</span>
            </div>
        </a>

        <nav class="hidden h-full items-center gap-12 lg:flex">
            @foreach ($links as $key => $link)
                @php
                    // Mengecek apakah rute saat ini sama dengan rute pada menu
                    $isActive = request()->routeIs($link['route']);
                @endphp
                <a
                    href="{{ route($link['route']) }}"
                    class="relative flex h-full items-center text-label {{ $isActive ? 'font-semibold text-primary' : 'font-medium text-text-secondary hover:text-text' }}"
                >
                    {{ $link['label'] }}
                    @if ($isActive)
                        <span class="absolute inset-x-0 bottom-0 h-[3px] bg-primary"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="flex min-w-0 items-center gap-1 sm:gap-2 lg:gap-4">
            <x-theme-toggle />

            @auth
                {{-- Tampilan jika USER SUDAH LOGIN --}}
                <a href="{{ route('notifications.index') }}" class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full hover:bg-surface-muted" aria-label="Notifikasi">
                    <span aria-hidden="true">🔔</span>
                    @if (auth()->user()->notifications()->whereNull('read_at')->exists())
                        <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-danger-text"></span>
                    @endif
                </a>

                <a href="#" class="flex min-w-0 items-center gap-2.5 rounded-lg p-1.5 transition-colors hover:bg-surface-muted lg:-mr-1.5" title="Profil Saya">
                    <div class="h-9 w-9 shrink-0 rounded-full bg-surface-muted"></div>
                    <div class="hidden min-w-0 flex-col leading-tight sm:flex">
                        <span class="max-w-[200px] truncate text-[14px] font-semibold text-text">{{ auth()->user()->name }}</span>
                        <span class="text-[11px] text-text-secondary">NIM. {{ auth()->user()->nim ?? '—' }}</span>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                    @csrf
                    <button type="submit" class="text-small font-semibold text-text-secondary hover:text-primary">Keluar</button>
                </form>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.index') }}" class="hidden text-small font-semibold text-primary lg:inline">Admin</a>
                @endif
            @else
                {{-- Tampilan jika BELUM LOGIN (Tamu) --}}
                <a href="{{ route('login') }}" class="flex items-center gap-2.5 rounded-lg p-1.5 transition-colors hover:bg-surface-muted lg:-mr-1.5" title="Pergi ke halaman Login">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface-muted text-text-secondary">
                        <span aria-hidden="true">👤</span>
                    </div>
                    <div class="hidden flex-col leading-tight sm:flex">
                        <span class="text-[14px] font-semibold text-text">Pengguna Tamu</span>
                        <span class="text-[11px] text-primary font-medium">Klik untuk Masuk &rarr;</span>
                    </div>
                </a>
            @endauth

            <button
                type="button"
                data-mobile-menu-toggle
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="Buka menu"
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-text-secondary hover:bg-surface-muted lg:hidden"
            >
                <x-admin.icon name="menu" data-icon-open />
                <x-admin.icon name="x" class="hidden h-5 w-5 shrink-0" data-icon-close />
            </button>
        </div>
    </div>

    {{-- Panel menu layar kecil (dibuka/ditutup oleh resources/js/app.js) --}}
    <nav id="mobile-menu" hidden class="border-t border-border bg-surface-white px-4 pb-4 pt-2 sm:px-8 lg:hidden" aria-label="Menu utama">
        <div class="flex flex-col">
            @foreach ($links as $key => $link)
                @php $isActive = request()->routeIs($link['route']); @endphp
                <a
                    href="{{ route($link['route']) }}"
                    @if ($isActive) aria-current="page" @endif
                    class="rounded-sm px-3 py-3 text-label {{ $isActive ? 'bg-primary/10 font-semibold text-primary' : 'font-medium text-text-secondary hover:bg-surface-muted hover:text-text' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach

            @auth
                <div class="mt-2 flex flex-col border-t border-border pt-2">
                    <p class="px-3 py-2 text-small text-text-muted sm:hidden">
                        Masuk sebagai <span class="font-semibold text-text">{{ auth()->user()->name }}</span>
                    </p>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.index') }}" class="rounded-sm px-3 py-3 text-label font-semibold text-primary hover:bg-surface-muted">Panel Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-sm px-3 py-3 text-left text-label font-medium text-text-secondary hover:bg-surface-muted hover:text-text">Keluar</button>
                    </form>
                </div>
            @else
                <div class="mt-2 border-t border-border pt-2 sm:hidden">
                    <a href="{{ route('login') }}" class="block rounded-sm px-3 py-3 text-label font-semibold text-primary hover:bg-surface-muted">Masuk &rarr;</a>
                </div>
            @endauth
        </div>
    </nav>
</header>
