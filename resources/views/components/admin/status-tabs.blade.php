@props([
    'route',              // nama rute halaman daftar, misal 'admin.lost.index'
    'counts',             // jumlah per status: ['pending' => 3, ...]
    'current' => null,    // status yang sedang dipilih (null = semua)
    'query' => [],        // filter lain yang ikut dibawa saat pindah tab
])

@php
    use App\Services\ReportStatistics;

    $tabs = ['' => 'Semua'] + ReportStatistics::STATUSES;
@endphp

<nav class="flex gap-1 overflow-x-auto border-b border-border" aria-label="Filter status">
    @foreach ($tabs as $value => $label)
        @php
            $active = (string) $current === (string) $value;
            $count = $value === '' ? collect($counts)->sum() : ($counts[$value] ?? 0);
            $params = array_filter(['status' => $value] + $query, fn ($v) => $v !== null && $v !== '');
        @endphp
        <a
            href="{{ route($route, $params) }}"
            @if ($active) aria-current="page" @endif
            class="-mb-px flex shrink-0 items-center gap-2 border-b-2 px-3 py-2.5 text-small transition-colors {{ $active ? 'border-primary font-semibold text-primary' : 'border-transparent font-medium text-text-secondary hover:text-text' }}"
        >
            {{ $label }}
            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $active ? 'bg-primary/10' : 'bg-surface-muted' }}">{{ $count }}</span>
        </a>
    @endforeach
</nav>
