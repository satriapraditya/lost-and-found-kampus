@props([
    'type' => 'bar',   // bar | line | doughnut
    'labels' => [],
    'series' => [],    // [['label' => 'Hilang', 'data' => [..], 'color' => 'lost'], ...]
    'height' => 280,
    'label' => '',     // deskripsi singkat untuk pembaca layar
])

{{--
    Grafik Chart.js. Warna seri memakai nama peran (lihat SERIES_COLORS di
    resources/js/admin.js) supaya satu entitas selalu satu warna di semua grafik.
--}}
<div class="relative w-full" style="height: {{ $height }}px">
    <canvas
        role="img"
        aria-label="{{ $label }}"
        data-chart="{{ json_encode(['type' => $type, 'labels' => array_values($labels), 'series' => $series]) }}"
    ></canvas>
</div>
