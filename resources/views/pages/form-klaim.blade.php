<x-layouts.app title="Klaim Barang" active="beranda">

    <section class="w-full px-4 py-8 sm:px-8 sm:py-10 lg:px-20 lg:py-14">
        <div class="mx-auto flex max-w-[800px] flex-col gap-6">

            <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-small text-text-secondary" aria-label="Breadcrumb">
                <a href="{{ route('beranda') }}" class="hover:text-primary">Beranda</a>
                <span aria-hidden="true">›</span>
                <a href="{{ route('barang.detail', $item->id) }}" class="hover:text-primary">Detail Barang</a>
                <span aria-hidden="true">›</span>
                <span class="font-semibold text-primary">Klaim Barang</span>
            </nav>

            @if (session('submitted'))
                {{-- State: konfirmasi setelah form dikirim (data simulasi) --}}
                <div class="flex flex-col items-center gap-4 rounded-lg border border-success-text bg-success-bg p-6 text-center sm:p-10">
                    <span class="text-4xl" aria-hidden="true">✅</span>
                    <h1 class="text-h2 font-bold text-success-text">Klaim Berhasil Diajukan</h1>
                    <p class="max-w-md text-label text-text-secondary">
                        Klaim kamu untuk                         <strong>{{ $item->title }}</strong> sudah masuk ke antrean admin. Pantau status pengajuan melalui halaman Riwayat dan Notifikasi.
                    </p>
                    <x-button variant="primary" href="{{ route('riwayat') }}">Lihat Riwayat Klaim</x-button>
                </div>
            @else
                <div class="flex items-start gap-3 rounded-md border border-primary bg-primary/10 p-4">
                    <span aria-hidden="true">ℹ️</span>
                    <p class="text-small leading-relaxed text-primary">
                        Klaim Anda akan diverifikasi secara manual oleh admin Lost &amp; Found kampus. Proses ini membutuhkan bukti kepemilikan yang valid sebelum barang diserahkan secara langsung.
                    </p>
                </div>

                <div class="flex flex-col gap-6 rounded-lg border border-border bg-surface-white p-5 shadow-card sm:p-8">
                    <div class="flex flex-col gap-1">
                        <h1 class="text-[20px] font-extrabold text-text sm:text-[22px]">Ajukan Klaim Kepemilikan</h1>
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

                        <div class="break-words rounded-lg bg-surface p-3 text-small text-text-secondary">
                            Klaim diajukan atas nama <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }}).
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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

                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
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
