<x-layouts.app title="Klaim Barang" active="beranda">

    <section class="w-full px-20 py-14">
        <div class="mx-auto flex max-w-[800px] flex-col gap-6">

            <nav class="flex items-center gap-2 text-small text-text-secondary" aria-label="Breadcrumb">
                <a href="{{ route('beranda') }}" class="hover:text-primary">Beranda</a>
                <span aria-hidden="true">›</span>
                <a href="{{ route('barang.detail', $item->id) }}" class="hover:text-primary">Detail Barang</a>
                <span aria-hidden="true">›</span>
                <span class="font-semibold text-primary">Klaim Barang</span>
            </nav>

            @if (session('submitted'))
                {{-- State: konfirmasi setelah form dikirim (data simulasi) --}}
                <div class="flex flex-col items-center gap-4 rounded-lg border border-success-text bg-success-bg p-10 text-center">
                    <span class="text-4xl" aria-hidden="true">✅</span>
                    <h1 class="text-h2 font-bold text-success-text">Klaim Berhasil Diajukan</h1>
                    <p class="max-w-md text-label text-text-secondary">
                        Klaim kamu untuk                         <strong>{{ $item->title }}</strong> sudah masuk ke antrean admin. Pantau status pengajuan melalui halaman Riwayat dan Notifikasi.
                    </p>
                    <x-button variant="primary" href="{{ route('riwayat') }}">Lihat Riwayat Klaim</x-button>
                </div>
            @else
                <div class="flex items-center gap-3 rounded-md border border-primary bg-[#f5f3ff] p-4">
                    <span aria-hidden="true">ℹ️</span>
                    <p class="text-small leading-relaxed text-primary">
                        Klaim Anda akan diverifikasi secara manual oleh admin Lost &amp; Found kampus. Proses ini membutuhkan bukti kepemilikan yang valid sebelum barang diserahkan secara langsung.
                    </p>
                </div>

                <div class="flex flex-col gap-6 rounded-lg border border-border bg-surface-white p-8 shadow-card">
                    <div class="flex flex-col gap-1">
                        <h1 class="text-[22px] font-extrabold text-text">Ajukan Klaim Kepemilikan</h1>
                        <p class="text-label text-text-secondary">
                            Mengklaim barang: <span class="font-bold text-primary">{{ $item->title }}</span>
                        </p>
                    </div>

                    <hr class="border-border" />

                    <form method="POST" action="{{ route('klaim.store', $item->id) }}" class="flex flex-col gap-5">
                        @csrf

                        <x-form-field
                            type="textarea"
                            name="ciri_khusus"
                            label="Ciri Khusus Barang"
                            placeholder="Tuliskan ciri rahasia di sini untuk diverifikasi oleh admin..."
                            required
                            :value="old('ciri_khusus')"
                            :error="$errors->first('ciri_khusus')"
                        />
                        <p class="-mt-3 text-caption text-text-secondary">
                            Jelaskan ciri khusus yang tidak tercantum di halaman publik (misal: passcode, IMEI, serial number, atau detail isi di dalam casing).
                        </p>

                        <hr class="border-border" />
                        <p class="text-[15px] font-bold text-text">Data Diri Pengklaim</p>

                        <div class="rounded-lg bg-slate-50 p-3 text-sm text-slate-600">
                            Klaim diajukan atas nama <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }}).
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-field
                                type="tel"
                                name="whatsapp"
                                label="Nomor WhatsApp"
                                placeholder="081234567890"
                                required
                                :value="old('whatsapp', auth()->user()->phone)"
                                :error="$errors->first('whatsapp')"
                            />
                        </div>

                        <div class="flex justify-end gap-3">
                            <x-button variant="secondary" href="{{ route('barang.detail', $item->id) }}">Batal</x-button>
                            <x-button type="submit" variant="primary">Ajukan Klaim</x-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </section>

</x-layouts.app>

{
