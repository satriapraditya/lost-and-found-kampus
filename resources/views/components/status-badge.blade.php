@props(['status'])

@php
    // Warna bukan satu-satunya pembeda — teks label selalu ikut tampil,
    // supaya tetap jelas dibaca (sesuai kriteria pemeriksaan modul).
    $map = [
        'tersedia'  => ['bg' => 'bg-success-bg', 'text' => 'text-success-text', 'label' => 'Tersedia'],
        'diklaim'   => ['bg' => 'bg-danger-bg',  'text' => 'text-danger-text',  'label' => 'Diklaim'],
        'menunggu'  => ['bg' => 'bg-warning-bg', 'text' => 'text-warning-text', 'label' => 'Menunggu'],
        'disetujui' => ['bg' => 'bg-success-bg', 'text' => 'text-success-text', 'label' => 'Disetujui'],
        'ditolak'   => ['bg' => 'bg-danger-bg',  'text' => 'text-danger-text',  'label' => 'Ditolak'],
        'selesai'   => ['bg' => 'bg-surface-muted', 'text' => 'text-text-secondary', 'label' => 'Selesai'],
    ];

    $style = $map[$status] ?? $map['menunggu'];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-caption font-semibold uppercase {{ $style['bg'] }} {{ $style['text'] }}">
    {{ $style['label'] }}
</span>

{{--
    Contoh pakai:
    <x-status-badge status="tersedia" />
    <x-status-badge status="diklaim" />
    <x-status-badge status="menunggu" />
--}}
