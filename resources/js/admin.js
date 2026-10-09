import './bootstrap';
import {
    Chart,
    BarController,
    BarElement,
    LineController,
    LineElement,
    PointElement,
    DoughnutController,
    ArcElement,
    CategoryScale,
    LinearScale,
    Filler,
    Legend,
    Tooltip,
} from 'chart.js';

Chart.register(
    BarController, BarElement, LineController, LineElement, PointElement,
    DoughnutController, ArcElement, CategoryScale, LinearScale, Filler, Legend, Tooltip,
);

/* ==========================================================
   Sidebar (layar kecil)
   ========================================================== */
const sidebar = document.getElementById('admin-sidebar');
const overlay = document.querySelector('[data-sidebar-overlay]');

function setSidebar(open) {
    sidebar?.classList.toggle('-translate-x-full', !open);
    overlay?.classList.toggle('hidden', !open);
}

document.querySelector('[data-sidebar-open]')?.addEventListener('click', () => setSidebar(true));
document.querySelector('[data-sidebar-close]')?.addEventListener('click', () => setSidebar(false));
overlay?.addEventListener('click', () => setSidebar(false));

/* ==========================================================
   Warna grafik
   Warna per peran, bukan per urutan: "Barang Temuan" selalu ungu
   di grafik mana pun. Kedua palet (terang & gelap) sudah dicek aman
   untuk buta warna. Di mode terang kuning & hijau kontrasnya rendah,
   jadi grafik selalu disertai legenda/angka.
   ========================================================== */
const PALETTES = {
    light: {
        series: { found: '#7c3aed', lost: '#eb6834', completed: '#1baf7a' },
        categorical: ['#7c3aed', '#eb6834', '#1baf7a', '#eda100', '#2a78d6'],
        other: '#9ca3af',
        ink: { text: '#4b5563', muted: '#9ca3af', grid: '#f3f4f6', axis: '#e5e7eb', surface: '#ffffff', tooltip: '#111827' },
    },
    dark: {
        series: { found: '#9b6dfa', lost: '#d95926', completed: '#199e70' },
        categorical: ['#9b6dfa', '#d95926', '#199e70', '#c98500', '#3987e5'],
        other: '#6b7280',
        ink: { text: '#d1d5db', muted: '#9ca3af', grid: '#1f2937', axis: '#273244', surface: '#111827', tooltip: '#000000' },
    },
};

const currentTheme = () => (document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');

Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
Chart.defaults.font.size = 12;

const legend = {
    position: 'top',
    align: 'end',
    labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 16 },
};

function tooltip(p) {
    return {
        backgroundColor: p.ink.tooltip,
        titleColor: '#ffffff',
        bodyColor: '#ffffff',
        padding: 10,
        cornerRadius: 8,
        boxPadding: 4,
        usePointStyle: true,
    };
}

function cartesianScales(p) {
    return {
        x: { grid: { display: false }, border: { color: p.ink.axis }, ticks: { color: p.ink.muted } },
        y: {
            beginAtZero: true,
            grid: { color: p.ink.grid },
            border: { display: false },
            ticks: { color: p.ink.muted, precision: 0, maxTicksLimit: 6 },
        },
    };
}

/* ==========================================================
   Pembuat grafik
   ========================================================== */
function buildBar({ labels, series }, p) {
    return {
        type: 'bar',
        data: {
            labels,
            datasets: series.map((s) => ({
                label: s.label,
                data: s.data,
                backgroundColor: p.series[s.color] ?? s.color,
                borderRadius: 4,
                borderSkipped: 'start',
                maxBarThickness: 18,
                categoryPercentage: 0.6,
                barPercentage: 0.85,
            })),
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { ...legend, display: series.length > 1 }, tooltip: tooltip(p) },
            scales: cartesianScales(p),
        },
    };
}

function buildLine({ labels, series }, p) {
    return {
        type: 'line',
        data: {
            labels,
            datasets: series.map((s) => {
                const color = p.series[s.color] ?? s.color;
                return {
                    label: s.label,
                    data: s.data,
                    borderColor: color,
                    backgroundColor: color,
                    borderWidth: 2,
                    cubicInterpolationMode: 'monotone',
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    pointHoverBorderColor: p.ink.surface,
                    pointHoverBorderWidth: 2,
                };
            }),
        },
        options: {
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { ...legend, display: series.length > 1 }, tooltip: tooltip(p) },
            scales: cartesianScales(p),
        },
    };
}

function buildDoughnut({ labels, series }, p) {
    return {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data: series[0]?.data ?? [],
                backgroundColor: labels.map((label, i) => categoryColor(p, i, label === 'Lainnya')),
                borderColor: p.ink.surface,
                borderWidth: 2,
                hoverOffset: 4,
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '68%',
            // Legenda ditulis di HTML (dengan angka & persen), jadi legend bawaan dimatikan
            plugins: { legend: { display: false }, tooltip: tooltip(p) },
        },
    };
}

function categoryColor(p, index, isOther) {
    return isOther ? p.other : p.categorical[index % p.categorical.length];
}

const builders = { bar: buildBar, line: buildLine, doughnut: buildDoughnut };
let charts = [];

function renderCharts() {
    const p = PALETTES[currentTheme()];
    Chart.defaults.color = p.ink.text;

    charts.forEach((chart) => chart.destroy());
    charts = [];

    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
        const config = JSON.parse(canvas.dataset.chart);
        const build = builders[config.type];

        if (build) {
            charts.push(new Chart(canvas, build(config, p)));
        }
    });

    // Titik warna di legenda HTML donut, supaya sama persis dengan grafiknya
    document.querySelectorAll('[data-category-swatch]').forEach((el) => {
        el.style.backgroundColor = categoryColor(p, Number(el.dataset.categorySwatch), el.dataset.other !== undefined);
    });
}

renderCharts();

/* ==========================================================
   Mode gelap / terang
   Tema awal sudah dipasang oleh skrip kecil di <head> layout admin.
   ========================================================== */
document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
    const next = currentTheme() === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = next;

    try {
        localStorage.setItem('admin-theme', next);
    } catch (e) {
        // Mode privat / penyimpanan diblokir: tema tetap berganti, hanya tidak diingat
    }

    renderCharts();
});
