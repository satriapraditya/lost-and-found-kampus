<x-layouts.app>
    <div class="bg-slate-50 min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">

            {{-- Breadcrumb --}}
            <nav class="text-xs text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="hover:text-violet-600">Beranda</a>
                <span class="mx-1">&rsaquo;</span>
                <span class="text-violet-600 font-medium">Laporkan Barang Temuan</span>
            </nav>

            {{-- Banner info --}}
            <div class="flex items-start gap-3 rounded-lg border border-violet-300 bg-violet-50 px-4 py-3 mb-5">
                <svg class="w-4 h-4 mt-0.5 shrink-0 text-violet-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <p class="text-xs text-violet-800 leading-relaxed">
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
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h1 class="text-lg font-semibold text-slate-900">Laporkan Barang Temuan</h1>
                </div>

                <form action="{{ url('/lapor-temuan') }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-5">
                    @csrf

                    {{-- Foto --}}
                    <div>
                        <label for="foto" class="block text-xs font-semibold text-slate-700 mb-2">Foto Barang Temuan</label>
                        <label for="foto"
                               class="flex flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed px-4 py-8 text-center cursor-pointer transition-colors hover:bg-violet-50 focus-within:ring-2 focus-within:ring-violet-500
                                      {{ $errors->has('foto') ? 'border-red-400 bg-red-50' : 'border-violet-300 bg-violet-50/40' }}">
                            <img id="foto-preview" class="hidden max-h-40 rounded-md mb-2" alt="Pratinjau foto barang">
                            <svg id="foto-icon" class="w-7 h-7 text-violet-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="16" rx="2" />
                                <circle cx="9" cy="10" r="1.5" />
                                <path d="M21 16l-5-5-8 8" />
                            </svg>
                            <span id="foto-label" class="text-xs font-semibold text-violet-600">Klik untuk unggah atau seret foto ke sini</span>
                            <span class="text-[11px] text-slate-500">Maksimal ukuran file 5MB (Format: JPG, PNG)</span>
                            <input id="foto" name="foto" type="file" accept="image/jpeg,image/png" class="sr-only">
                        </label>
                        @error('foto')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama barang --}}
                    <div>
                        <label for="nama_barang" class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                        <input id="nama_barang" name="nama_barang" type="text" value="{{ old('nama_barang') }}"
                               placeholder="Contoh: Kunci Motor Honda, Tumbler Corkcicle, etc."
                               class="w-full rounded-lg border px-3 py-2 text-sm bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500
                                      {{ $errors->has('nama_barang') ? 'border-red-400' : 'border-slate-200' }}">
                        @error('nama_barang')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Ciri-ciri <span class="text-red-500">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="4"
                                  placeholder="Tuliskan detail fisik barang (misal: warna, stiker yang menempel, kondisi). Jangan menuliskan detail yang terlalu rahasia agar bisa digunakan sebagai validasi saat klaim."
                                  class="w-full rounded-lg border px-3 py-2 text-sm bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500
                                         {{ $errors->has('deskripsi') ? 'border-red-400' : 'border-slate-200' }}">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori + Lokasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="kategori" class="block text-xs font-semibold text-slate-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select id="kategori" name="kategori"
                                    class="w-full rounded-lg border px-3 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-violet-500
                                           {{ $errors->has('kategori') ? 'border-red-400' : 'border-slate-200' }}">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('kategori') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('kategori')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="lokasi" class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Ditemukan <span class="text-red-500">*</span></label>
                            <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi') }}"
                                   placeholder="Misal: Perpustakaan Lt. 2, Mushola FT"
                                   class="w-full rounded-lg border px-3 py-2 text-sm bg-slate-50 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500
                                          {{ $errors->has('lokasi') ? 'border-red-400' : 'border-slate-200' }}">
                            @error('lokasi')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label for="tanggal" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Ditemukan <span class="text-red-500">*</span></label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal') }}" max="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border px-3 py-2 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-violet-500
                                      {{ $errors->has('tanggal') ? 'border-red-400' : 'border-slate-200' }}">
                        @error('tanggal')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <p class="text-xs text-slate-500">Laporan akan dikaitkan dengan akun {{ auth()->user()->name }}.</p>

                    {{-- Tombol --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ url('/') }}"
                           class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-violet-500">
                            Batal
                        </a>
                        <button type="submit"
                                class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-medium text-white hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">
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