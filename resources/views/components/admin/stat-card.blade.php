@props([
    'label',
    'value',
    'icon' => 'chart',
    'hint' => null,
])

<div class="flex items-start justify-between gap-3 rounded-lg border border-border bg-surface-white p-5 shadow-card">
    <div class="flex min-w-0 flex-col gap-1">
        <p class="text-small font-medium text-text-secondary">{{ $label }}</p>
        <p class="text-[28px] font-extrabold leading-tight text-text">{{ $value }}</p>
        @if ($hint)
            <p class="text-caption text-text-muted">{{ $hint }}</p>
        @endif
    </div>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
        <x-admin.icon :name="$icon" />
    </div>
</div>

{{--
    Contoh pakai:
    <x-admin.stat-card label="Barang Hilang" :value="12" icon="search" hint="3 dilaporkan bulan ini" />
--}}
