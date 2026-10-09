@php
    use App\Services\ReportStatistics;

    $period = match (true) {
        isset($filters['from'], $filters['to']) => \Illuminate\Support\Carbon::parse($filters['from'])->locale('id')->translatedFormat('d M Y') . ' – ' . \Illuminate\Support\Carbon::parse($filters['to'])->locale('id')->translatedFormat('d M Y'),
        isset($filters['from']) => 'Sejak ' . \Illuminate\Support\Carbon::parse($filters['from'])->locale('id')->translatedFormat('d M Y'),
        isset($filters['to']) => 'Sampai ' . \Illuminate\Support\Carbon::parse($filters['to'])->locale('id')->translatedFormat('d M Y'),
        default => 'Semua waktu',
    };
@endphp

{{-- Versi cetak untuk "Export PDF": dibuka di tab baru lalu dialog print muncul otomatis. --}}
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Lost &amp; Found — {{ now()->format('Y-m-d') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @page { size: A4 landscape; margin: 14mm; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
        /* Supaya header tabel diulang di tiap halaman PDF */
        thead { display: table-header-group; }
        tr { break-inside: avoid; }
    </style>
</head>
<body class="bg-white text-text antialiased" onload="window.print()">
    <div class="mx-auto flex max-w-[1100px] flex-col gap-6 p-8 print:p-0">
        <div class="no-print flex items-center justify-between rounded-md bg-info-bg px-4 py-3 text-small text-info-text">
            <span>Pilih <strong>"Simpan sebagai PDF"</strong> sebagai printer untuk menyimpan file PDF.</span>
            <button type="button" onclick="window.print()" class="rounded-sm bg-primary px-4 py-2 font-semibold text-white hover:bg-primary-hover">Cetak / Simpan PDF</button>
        </div>

        <header class="flex items-start justify-between gap-6 border-b-2 border-primary pb-4">
            <div>
                <p class="text-caption font-semibold uppercase tracking-wider text-primary">Campus Find — Lost &amp; Found Kampus</p>
                <h1 class="text-[22px] font-extrabold text-text">Laporan Barang Hilang &amp; Temuan</h1>
            </div>
            <dl class="grid grid-cols-[auto_auto] gap-x-3 gap-y-0.5 text-small">
                <dt class="text-text-muted">Periode</dt><dd class="font-semibold">{{ $period }}</dd>
                <dt class="text-text-muted">Jenis</dt><dd class="font-semibold">{{ ReportStatistics::TYPES[$filters['type'] ?? ''] ?? 'Semua' }}</dd>
                <dt class="text-text-muted">Kategori</dt><dd class="font-semibold">{{ $categoryName ?? 'Semua' }}</dd>
                <dt class="text-text-muted">Status</dt><dd class="font-semibold">{{ ReportStatistics::STATUSES[$filters['status'] ?? ''] ?? 'Semua' }}</dd>
                <dt class="text-text-muted">Dicetak</dt><dd class="font-semibold">{{ now()->locale('id')->translatedFormat('d M Y, H:i') }}</dd>
            </dl>
        </header>

        <div class="grid grid-cols-5 gap-3">
            @foreach ([
                'Total Laporan' => $summary['total'],
                'Barang Hilang' => $summary['lost'],
                'Barang Temuan' => $summary['found'],
                'Selesai' => $summary['returned'],
                'Tingkat Penyelesaian' => $summary['return_rate'] . '%',
            ] as $label => $value)
                <div class="rounded-md border border-border px-4 py-3">
                    <p class="text-caption text-text-secondary">{{ $label }}</p>
                    <p class="text-[22px] font-extrabold text-text">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        <table class="w-full border-collapse text-left text-small">
            <thead>
                <tr class="bg-surface-muted text-caption uppercase tracking-wide text-text-secondary">
                    <th class="border border-border px-3 py-2">No</th>
                    <th class="border border-border px-3 py-2">Barang</th>
                    <th class="border border-border px-3 py-2">Kategori</th>
                    <th class="border border-border px-3 py-2">Jenis</th>
                    <th class="border border-border px-3 py-2">Lokasi</th>
                    <th class="border border-border px-3 py-2">Pelapor</th>
                    <th class="border border-border px-3 py-2">Tanggal Masuk</th>
                    <th class="border border-border px-3 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td class="border border-border px-3 py-2">{{ $loop->iteration }}</td>
                        <td class="border border-border px-3 py-2 font-semibold">{{ $report->item_name }}</td>
                        <td class="border border-border px-3 py-2">{{ $report->category->name ?? '—' }}</td>
                        <td class="border border-border px-3 py-2">{{ ReportStatistics::TYPES[$report->type] ?? $report->type }}</td>
                        <td class="border border-border px-3 py-2">{{ $report->location->name ?? '—' }}</td>
                        <td class="border border-border px-3 py-2">{{ $report->user->name ?? '—' }}</td>
                        <td class="border border-border px-3 py-2">{{ $report->created_at->format('d/m/Y') }}</td>
                        <td class="border border-border px-3 py-2">{{ ReportStatistics::STATUSES[$report->status] ?? $report->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-border px-3 py-6 text-center text-text-muted">Tidak ada laporan yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
