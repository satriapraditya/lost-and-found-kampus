<x-layouts.app title="Panel Admin">
    <main class="mx-auto max-w-6xl space-y-10 px-4 py-10">
        <div>
            <h1 class="text-2xl font-bold text-text">Panel Moderasi</h1>
            <p class="mt-1 text-text-secondary">Tinjau laporan dan klaim, lalu catat penyerahan barang.</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800" role="status">{{ session('success') }}</div>
        @endif

        <section class="space-y-4">
            <h2 class="text-xl font-bold text-text">Laporan menunggu ({{ $reports->count() }})</h2>
            @forelse ($reports as $report)
                <article class="space-y-4 rounded-lg border border-border bg-white p-5 shadow-card">
                    <div class="flex flex-wrap justify-between gap-4">
                        <div>
                            <h3 class="font-bold">{{ $report->item_name }}</h3>
                            <p class="text-sm text-text-secondary">{{ $report->category->name }} · {{ $report->location->name }} · {{ $report->user->name }} ({{ $report->user->email }})</p>
                            <p class="mt-2 text-sm">{{ $report->description }}</p>
                            <p class="mt-1 text-xs text-text-secondary">Tanggal ditemukan: {{ $report->event_date->format('d-m-Y') }} · WhatsApp: {{ $report->user->phone ?? 'Belum diisi' }}</p>
                            @if ($report->images->isNotEmpty())
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($report->images->first()->image_path) }}" alt="Foto {{ $report->item_name }}" class="mt-3 h-28 rounded object-cover">
                            @endif
                        </div>
                        <a class="text-sm font-semibold text-primary" href="{{ route('barang.detail', $report) }}">Lihat laporan</a>
                    </div>
                    <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        @method('PATCH')
                        <label class="min-w-[240px] flex-1 text-sm">Catatan
                            <input name="note" maxlength="2000" class="mt-1 w-full rounded border border-border px-3 py-2">
                        </label>
                        <button name="status" value="approved" class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Setujui</button>
                        <button name="status" value="rejected" class="rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white">Tolak</button>
                    </form>
                </article>
            @empty
                <p class="rounded-lg border border-dashed border-border p-5 text-text-secondary">Tidak ada laporan yang menunggu tinjauan.</p>
            @endforelse
        </section>

        <section class="space-y-4">
            <h2 class="text-xl font-bold text-text">Klaim aktif ({{ $claims->count() }})</h2>
            @forelse ($claims as $claim)
                <article class="space-y-4 rounded-lg border border-border bg-white p-5 shadow-card">
                    <div>
                        <h3 class="font-bold">{{ $claim->report->item_name }}</h3>
                        <p class="text-sm text-text-secondary">Pengklaim: {{ $claim->user->name }} ({{ $claim->user->email }}) · WhatsApp {{ $claim->whatsapp }}</p>
                        <p class="mt-2 text-sm"><strong>Bukti kepemilikan:</strong> {{ $claim->proof_description }}</p>
                        @if ($claim->admin_note)
                            <p class="mt-1 text-sm text-text-secondary">Catatan admin: {{ $claim->admin_note }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.claims.update', $claim) }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        @method('PATCH')
                        <label class="min-w-[240px] flex-1 text-sm">Catatan
                            <input name="note" maxlength="2000" class="mt-1 w-full rounded border border-border px-3 py-2">
                        </label>
                        @if ($claim->status === 'pending')
                            <button name="status" value="approved" class="rounded bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Setujui klaim</button>
                            <button name="status" value="rejected" class="rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white">Tolak klaim</button>
                        @else
                            <button name="status" value="completed" class="rounded bg-violet-600 px-4 py-2 text-sm font-semibold text-white">Tandai barang diserahkan</button>
                        @endif
                    </form>
                </article>
            @empty
                <p class="rounded-lg border border-dashed border-border p-5 text-text-secondary">Tidak ada klaim aktif.</p>
            @endforelse
        </section>
    </main>
</x-layouts.app>
