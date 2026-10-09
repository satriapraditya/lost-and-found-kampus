<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Campus Find</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">

    {{-- Navbar Minimal --}}
    <header class="w-full border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('beranda') }}" class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-violet-600">
                    <span class="text-white text-sm" aria-hidden="true">🔍</span>
                </div>
                <span class="font-extrabold text-slate-900">Campus Find</span>
            </a>
            <a href="{{ route('beranda') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-violet-600">
                <span>&larr;</span> Kembali ke Beranda
            </a>
        </div>
    </header>

    {{-- Form Container --}}
    <main class="flex min-h-[calc(100vh-72px)] flex-col items-center justify-center p-4 my-8">
        <div class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-slate-900">Buat Akun Baru</h1>
                <p class="mt-2 text-sm text-slate-500">Lengkapi data diri Anda untuk menggunakan layanan Lost & Found.</p>
            </div>

            <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
                @csrf
                
                {{-- Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- NIM / NIP --}}
                <div>
                    <label for="nim" class="block text-sm font-semibold text-slate-700 mb-1">NIM / Nomor Induk Pegawai</label>
                    <input id="nim" name="nim" type="text" value="{{ old('nim') }}" required placeholder="Masukkan NIM atau NIP (Untuk Admin/Dosen)"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500">
                    @error('nim')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="study_program" class="block text-sm font-semibold text-slate-700 mb-1">Program Studi</label>
                    <input id="study_program" name="study_program" type="text" value="{{ old('study_program') }}" required placeholder="Masukkan program studi"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500">
                    @error('study_program')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="20" placeholder="08xxxxxxxxxx"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Email Institusi --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email Institusi / Kampus</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required placeholder="mahasiswa@kampus.ac.id"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Password Grid --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Kata Sandi</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required minlength="8" placeholder="Buat kata sandi"
                                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500">
                            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Ulangi kata sandi" 
                                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500">
                        </div>
                    </div>
                </div>

                {{-- Syarat & Ketentuan --}}
                <div class="pt-2">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="terms" value="1" required class="mt-1 h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        <span class="text-sm text-slate-600 leading-relaxed">Saya menyetujui Syarat & Ketentuan Penggunaan Sistem Lost & Found Kampus</span>
                    </label>
                    @error('terms')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="mt-4 w-full rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">
                    Daftar Akun
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-slate-600">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-violet-600 hover:text-violet-700 hover:underline">Masuk di sini</a>
            </div>
        </div>
    </main>
</body>
</html>