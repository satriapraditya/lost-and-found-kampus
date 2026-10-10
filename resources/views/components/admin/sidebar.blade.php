@php
    /*
    | Menu sidebar admin. Menu yang rutenya belum dibuat otomatis tampil
    | abu-abu dengan label "Segera" — begitu anggota lain menambahkan rute
    | dengan nama di bawah, menunya langsung aktif tanpa perlu ubah file ini.
    */
    $groups = [
        'Utama' => [
            ['label' => 'Dashboard', 'route' => 'admin.index', 'icon' => 'home', 'match' => ['admin.index', 'admin.queue']],
        ],
        'Barang' => [
            ['label' => 'Barang Hilang', 'route' => 'admin.lost.index', 'icon' => 'search'],
            ['label' => 'Barang Temuan', 'route' => 'admin.found.index', 'icon' => 'box'],
            ['label' => 'Verifikasi Klaim', 'route' => 'admin.claims.index', 'icon' => 'check'],
        ],
        'Administrator' => [
            ['label' => 'Kelola Admin', 'route' => 'admin.admins.index', 'icon' => 'shield'],
            ['label' => 'Daftar User', 'route' => 'admin.users.index', 'icon' => 'users'],
            ['label' => 'Statistik & Laporan', 'route' => 'admin.statistics', 'icon' => 'chart'],
        ],
    ];
@endphp

<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-40 flex w-[260px] -translate-x-full flex-col border-r border-border bg-surface-white transition-transform duration-200 lg:translate-x-0"
>
    <div class="flex h-[72px] items-center justify-between gap-2.5 border-b border-border px-5">
        <a href="{{ route('admin.index') }}" class="flex items-center gap-2.5">
            <div class="flex h-20 w-20 items-center justify-center rounded-md ">
                <img
                    src="{{ asset('images/logo_lostandfound.png') }}"
                    alt="Logo Campus Find"
                    class="h-20 w-20 object-contain"
                >
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-h3 font-extrabold text-text">Campus Find</span>
                <span class="text-[11px] font-medium tracking-wide text-primary">PANEL ADMIN</span>
            </div>
        </a>
        <button type="button" data-sidebar-close class="rounded-sm p-1.5 text-text-secondary hover:bg-surface-muted lg:hidden" aria-label="Tutup menu">
            <x-admin.icon name="x" />
        </button>
    </div>

    <nav class="flex flex-1 flex-col gap-6 overflow-y-auto px-3 py-5">
        @foreach ($groups as $groupLabel => $items)
            <div class="flex flex-col gap-1">
                <p class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-text-muted">{{ $groupLabel }}</p>

                @foreach ($items as $item)
                    @php
                        $exists = Route::has($item['route']);
                        // Aktif juga untuk sub-halaman, misal admin.claims.show (atau sesuai 'match')
                        $isActive = $exists && request()->routeIs(...($item['match'] ?? [$item['route'], str_replace('.index', '', $item['route']) . '.*']));
                    @endphp

                    @if ($exists)
                        <a
                            href="{{ route($item['route']) }}"
                            @if ($isActive) aria-current="page" @endif
                            class="flex items-center gap-3 rounded-sm px-3 py-2.5 text-label transition-colors {{ $isActive ? 'bg-primary/10 font-semibold text-primary' : 'font-medium text-text-secondary hover:bg-surface-muted hover:text-text' }}"
                        >
                            <x-admin.icon :name="$item['icon']" />
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="flex cursor-not-allowed items-center gap-3 rounded-sm px-3 py-2.5 text-label font-medium text-text-muted" title="Halaman ini sedang dikerjakan">
                            <x-admin.icon :name="$item['icon']" />
                            {{ $item['label'] }}
                            <span class="ml-auto rounded-full bg-surface-muted px-2 py-0.5 text-[10px] font-semibold uppercase">Segera</span>
                        </span>
                    @endif
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="border-t border-border p-3">
        <a href="{{ route('beranda') }}" class="flex items-center gap-3 rounded-sm px-3 py-2.5 text-label font-medium text-text-secondary hover:bg-surface-muted hover:text-text">
            <x-admin.icon name="external" />
            Lihat Situs Publik
        </a>
    </div>
</aside>
