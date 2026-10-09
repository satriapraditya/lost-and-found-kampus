<x-layouts.app>
    <div class="bg-surface min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">

            {{-- Breadcrumb --}}
            <nav class="text-xs text-text-secondary mb-4" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-primary">Beranda</a>
                <span class="mx-1">&rsaquo;</span>
                <span class="text-primary font-medium">Laporkan Barang Temuan</span>
            </nav>

            {{-- Banner info --}}
            <div class="flex items-start gap-3 rounded-lg border border-primary/40 bg-primary/10 px-4 py-3 mb-5">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <p class="text-xs text-primary leading-relaxed">
                    Laporan Anda akan ditinjau oleh admin sebelum ditampilkan di beranda publik. Harap isi data sebenar-benarnya untuk memudahkan pemilik barang mengenali aset mereka.
                </p>
            </div>

            {{-- Notifikasi sukses --}}
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 mb-5 text-sm text-emerald-700" role="status">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Kartu form --}}
            <div class="bg-surface-white rounded-xl border border-border shadow-sm">
                <div class="px-6 py-4 border-b border-border">
                    <h1 class="text-lg font-semibold text-text">Laporkan Barang Temuan</h1>
                </div>

                <form action="{{ url('/lapor-temuan') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-5">
                    @csrf

                    {{-- Foto --}}
                    <div>
                        <label for="foto" class="block text-xs font-semibold text-text-secondary mb-2">Foto Barang Temuan</label>
                        <label for="foto"
                               class="flex flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed px-4 py-8 text-center cursor-pointer transition-colors hover:bg-primary/10 focus-within:ring-2 focus-within:ring-primary
                                      {{ $errors->has('foto') ? 'border-danger-text bg-danger-bg' : 'border-primary/40 bg-primary/5' }}">
                            <img id="foto-preview" class="hidden max-h-40 rounded-md mb-2" alt="Pratinjau foto barang">
                            <svg id="foto-icon" class="w-7 h-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="16" rx="2" />
                                <circle cx="9" cy="10" r="1.5" />
                                <path d="M21 16l-5-5-8 8" />
                            </svg>
                            <span id="foto-label" class="text-xs font-semibold text-primary">Klik untuk unggah atau seret foto ke sini</span>
                            <span class="text-[11px] text-text-secondary">Maksimal ukuran file 5MB (Format: JPG, PNG)</span>
                            <input id="foto" name="foto" type="file" accept="image/jpeg,image/png" class="sr-only">
                        </label>
                        @error('foto')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama barang --}}
                    <div>
                        <label for="nama_barang" class="block text-xs font-semibold text-text-secondary mb-1">Nama Barang <span class="text-danger-text">*</span></label>
                        <input id="nama_barang" name="nama_barang" type="text" value="{{ old('nama_barang') }}"
                               placeholder="Contoh: Kunci Motor Honda, Tumbler Corkcicle, etc."
                               class="w-full rounded-lg border px-3 py-2 text-sm bg-surface placeholder-text-muted focus:outline-none focus:ring-2 focus:ring-primary
                                      {{ $errors->has('nama_barang') ? 'border-danger-text' : 'border-border' }}">
                        @error('nama_barang')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-xs font-semibold text-text-secondary mb-1">Deskripsi Ciri-ciri <span class="text-danger-text">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Tuliskan detail fisik barang (misal: warna, stiker yang menempel, kondisi). Jangan menuliskan detail yang terlalu rahasia agar bisa digunakan sebagai validasi saat klaim."
                                  class="w-full rounded-lg border px-3 py-2 text-sm bg-surface placeholder-text-muted focus:outline-none focus:ring-2 focus:ring-primary
                                         {{ $errors->has('deskripsi') ? 'border-danger-text' : 'border-border' }}">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori + Lokasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="kategori" class="block text-xs font-semibold text-text-secondary mb-1">Kategori <span class="text-danger-text">*</span></label>
                            <select id="kategori" name="kategori"
                                    class="w-full rounded-lg border px-3 py-2 text-sm bg-surface focus:outline-none focus:ring-2 focus:ring-primary
                                           {{ $errors->has('kategori') ? 'border-danger-text' : 'border-border' }}">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('kategori') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="lokasi" class="block text-xs font-semibold text-text-secondary mb-1">Lokasi Ditemukan <span class="text-danger-text">*</span></label>
                            <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi') }}"
                                   placeholder="Misal: Perpustakaan Lt. 2, Mushola FT"
                                   class="w-full rounded-lg border px-3 py-2 text-sm bg-surface placeholder-text-muted focus:outline-none focus:ring-2 focus:ring-primary
                                          {{ $errors->has('lokasi') ? 'border-danger-text' : 'border-border' }}">
                            @error('lokasi')
                                <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label for="tanggal" class="block text-xs font-semibold text-text-secondary mb-1">Tanggal Ditemukan <span class="text-danger-text">*</span></label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal') }}" max="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border px-3 py-2 text-sm bg-surface focus:outline-none focus:ring-2 focus:ring-primary
                                      {{ $errors->has('tanggal') ? 'border-danger-text' : 'border-border' }}">
                        @error('tanggal')
                            <p class="mt-1 text-xs text-danger-text">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama pelapor + WhatsApp --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_pelapor" class="block text-xs font-semibold text-slate-700 mb-1">Nama Pelapor <span class="text-red-500">*</span></label>
                            <input id="nama_pelapor" name="nama_pelapor" type="text" value="{{ old('nama_pelapor', 'Budi Santoso') }}"
                                   class="w-full rounded-lg border px-3 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-violet-500
                                          {{ $errors->has('nama_pelapor') ? 'border-red-400' : 'border-slate-200' }}">
                            @error('nama_pelapor')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kontak" class="block text-xs font-semibold text-slate-700 mb-1">Kontak Pelapor (No. WhatsApp) <span class="text-red-500">*</span></label>
                            <input id="kontak" name="kontak" type="tel" inputmode="numeric" value="{{ old('kontak', '081234567890') }}"
                                   class="w-full rounded-lg border px-3 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-violet-500
                                          {{ $errors->has('kontak') ? 'border-red-400' : 'border-slate-200' }}">
                            @error('kontak')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ url('/') }}"
                           class="rounded-lg border border-border bg-surface-white px-4 py-2 text-sm font-medium text-text-secondary hover:bg-surface-muted focus:outline-none focus:ring-2 focus:ring-primary">
                            Batal
                        </a>
                        <button type="submit"
                                class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Pratinjau foto setelah dipilih --}}
    <script>
        (function () {
            var input = document.getElementById('foto');
            var preview = document.getElementById('foto-preview');
            var icon = document.getElementById('foto-icon');
            var label = document.getElementById('foto-label');

            input.addEventListener('change', function () {
                var file = input.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran foto melebihi 5MB. Pilih foto yang lebih kecil.');
                    input.value = '';
                    return;
                }

                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
                icon.classList.add('hidden');
                label.textContent = file.name;
            });
        })();
    </script>
</x-layouts.app>