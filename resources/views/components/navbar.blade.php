@props(['active' => 'beranda'])

@php
    $links = [
        'beranda' => ['label' => 'Beranda', 'route' => 'beranda'],
        'lapor' => ['label' => 'Lapor Temuan', 'route' => 'lapor.create'],
        'riwayat' => ['label' => 'Riwayat Saya', 'route' => 'riwayat'],
    ];
@endphp

<header class="w-full border-b border-border bg-white">
    <div class="mx-auto flex h-[72px] max-w-[1440px] items-center justify-between px-20">
        <a href="{{ route('beranda') }}" class="flex items-center gap-2.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                <span class="text-white text-lg" aria-hidden="true">🔍</span>
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-h3 font-extrabold text-text">Campus Find</span>
                <span class="text-[11px] font-medium tracking-wide text-primary">LOST &amp; FOUND</span>
            </div>
        </a>

        <nav class="flex h-full items-center gap-8">
            @foreach ($links as $key => $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="relative flex h-full items-center text-label {{ $active === $key ? 'font-semibold text-primary' : 'font-medium text-text-secondary hover:text-text' }}"
                >
                    {{ $link['label'] }}
                    @if ($active === $key)
                        <span class="absolute inset-x-0 bottom-0 h-[3px] bg-primary"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-4">
            <button type="button" class="relative flex h-10 w-10 items-center justify-center rounded-full hover:bg-surface-muted" aria-label="Notifikasi">
                <span aria-hidden="true">🔔</span>
                <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-danger-text"></span>
            </button>
            <div class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-full bg-surface-muted"></div>
                <div class="flex flex-col leading-tight">
                    <span class="text-[14px] font-semibold text-text">{{ auth()->user()->name ?? 'Nama Pengguna' }}</span>
                    <span class="text-[11px] text-text-secondary">NIM. {{ auth()->user()->nim ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Contoh pakai: <x-navbar active="beranda" /> --}}
