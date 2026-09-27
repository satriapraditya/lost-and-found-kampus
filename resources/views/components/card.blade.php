@props([
    'title',
    'location',
    'timestamp',
    'status' => 'tersedia', // tersedia | diklaim
    'imageUrl' => null,
    'href' => '#',
    'variant' => 'normal', // normal | selected | unavailable
])

@php
    $isUnavailable = $variant === 'unavailable' || $status === 'diklaim';
    $ringClass = $variant === 'selected' ? 'ring-2 ring-primary' : '';
@endphp

<div class="flex flex-col overflow-hidden rounded-lg border border-border bg-white shadow-card {{ $ringClass }} {{ $isUnavailable ? 'opacity-75' : '' }}">
    <div class="h-[200px] w-full bg-surface-muted">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $title }}" class="h-full w-full object-cover" />
        @else
            <div class="flex h-full w-full items-center justify-center text-text-muted text-caption">Tidak ada foto</div>
        @endif
    </div>

    <div class="flex flex-col gap-4 p-5">
        <div class="flex items-center justify-between">
            <x-status-badge :status="$status" />
            <span class="text-caption text-text-muted">{{ $timestamp }}</span>
        </div>

        <div class="flex flex-col gap-1.5">
            <p class="truncate text-card-title font-bold text-text">{{ $title }}</p>
            <div class="flex items-center gap-1.5 text-small text-text-secondary">
                <span aria-hidden="true">📍</span>
                <span class="truncate">{{ $location }}</span>
            </div>
        </div>
    </div>

    @if ($isUnavailable)
        <div class="flex items-center justify-between bg-surface-muted px-5 py-3 text-small font-semibold text-text-muted cursor-not-allowed">
            Sudah Diklaim
        </div>
    @else
        <a href="{{ $href }}" class="flex items-center justify-between bg-surface-muted px-5 py-3 text-small font-semibold text-primary hover:bg-border transition-colors">
            Detail Selengkapnya
            <span aria-hidden="true">→</span>
        </a>
    @endif
</div>

{{--
    Contoh pakai:
    <x-card
        title="iPhone 13 Pro Max Abu-abu"
        location="Perpustakaan Lantai 2"
        timestamp="Kemarin, 14:20 WIB"
        status="tersedia"
        :href="route('barang.detail', $item->id)"
    />
    <x-card title="Kunci Motor Honda Hitam" location="Parkiran Gedung C" timestamp="23 Feb 2026" status="diklaim" variant="unavailable" />
--}}
