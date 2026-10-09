@props([
    'variant' => 'primary', // primary | secondary | ghost
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-sm px-5 py-2.5 text-label font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2';

    $variants = [
        'primary' => 'bg-primary text-white hover:bg-primary-hover disabled:bg-border disabled:text-text-muted',
        'secondary' => 'bg-surface-white text-text-secondary border border-border hover:bg-surface-muted disabled:opacity-50',
        'ghost' => 'bg-transparent text-primary hover:bg-surface-muted disabled:text-text-muted',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @if ($disabled) disabled @endif
        {{ $attributes->merge(['class' => $classes . ($disabled ? ' cursor-not-allowed' : ' cursor-pointer')]) }}
    >
        {{ $slot }}
    </button>
@endif

{{--
    Contoh pakai:
    <x-button variant="primary">Kirim Laporan</x-button>
    <x-button variant="secondary" href="/beranda">Batal</x-button>
    <x-button variant="primary" disabled>Memproses...</x-button>
--}}
