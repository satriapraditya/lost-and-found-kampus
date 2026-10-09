@props([
    'title' => 'Dashboard',
    'subtitle' => null,
])

{{--
    Layout semua halaman admin (sidebar + topbar).
    Contoh pakai:
    <x-layouts.admin title="Verifikasi Klaim" subtitle="Kelola klaim kepemilikan barang temuan.">
        <x-slot:actions> ...tombol di kanan judul... </x-slot:actions>
        ...isi halaman...
    </x-layouts.admin>
--}}

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Admin Campus Find</title>
    <script>
        // Pasang tema sebelum halaman digambar supaya tidak berkedip putih.
        // Pilihan tersimpan di localStorage; kalau belum pernah memilih, ikut pengaturan sistem.
        (function () {
            var theme = null;
            try { theme = localStorage.getItem('admin-theme'); } catch (e) {}
            if (theme !== 'light' && theme !== 'dark') {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.dataset.theme = theme;
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body class="min-h-screen bg-surface text-text antialiased">
    <x-admin.sidebar />

    {{-- Latar gelap saat sidebar dibuka di layar kecil --}}
    <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-black/50 lg:hidden"></div>

    <div class="flex min-h-screen flex-col lg:pl-[260px]">
        <header class="sticky top-0 z-20 flex h-[72px] items-center justify-between gap-4 border-b border-border bg-surface-white px-4 md:px-8">
            <button type="button" data-sidebar-open class="rounded-sm p-2 text-text-secondary hover:bg-surface-muted lg:hidden" aria-label="Buka menu">
                <x-admin.icon name="menu" />
            </button>

            <p class="hidden text-small text-text-muted lg:block">
                {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            </p>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    data-theme-toggle
                    class="flex h-10 w-10 items-center justify-center rounded-full text-text-secondary hover:bg-surface-muted"
                    aria-label="Ganti mode gelap/terang"
                    title="Ganti mode gelap/terang"
                >
                    {{-- Yang tampil adalah tujuan: bulan saat terang, matahari saat gelap --}}
                    <x-admin.icon name="moon" class="h-5 w-5 shrink-0 dark:hidden" />
                    <x-admin.icon name="sun" class="hidden h-5 w-5 shrink-0 dark:block" />
                </button>

                {{-- TODO (Adit): sambungkan ke notifikasi admin --}}
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full text-text-secondary hover:bg-surface-muted" aria-label="Notifikasi">
                    <x-admin.icon name="bell" />
                </button>

                {{-- TODO (Arsha): menu profil & logout --}}
                <div class="flex items-center gap-2.5 rounded-lg p-1.5">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-small font-bold text-white">
                        {{ strtoupper(mb_substr(auth()->user()->name ?? 'Admin', 0, 1)) }}
                    </div>
                    <div class="hidden flex-col leading-tight sm:flex">
                        <span class="text-[14px] font-semibold text-text">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="text-[11px] text-text-secondary">Administrator</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 px-4 py-8 md:px-8">
            <div class="mx-auto flex max-w-[1280px] flex-col gap-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div class="flex flex-col gap-1.5">
                        <h1 class="text-[24px] font-extrabold text-text">{{ $title }}</h1>
                        @if ($subtitle)
                            <p class="text-label text-text-secondary">{{ $subtitle }}</p>
                        @endif
                    </div>

                    @isset($actions)
                        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
                    @endisset
                </div>

                {{-- Pesan hasil aksi: ->with('success', ...) / ->with('error', ...) --}}
                @if (session('success'))
                    <div role="status" class="rounded-md border border-success-text/20 bg-success-bg px-5 py-3.5 text-small font-medium text-success-text">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div role="alert" class="rounded-md border border-danger-text/20 bg-danger-bg px-5 py-3.5 text-small font-medium text-danger-text">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
