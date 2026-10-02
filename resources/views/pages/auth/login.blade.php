<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Masuk - Campus Find</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">

    {{-- Navbar Minimal --}}
    <header class="w-full border-b border-slate-200 bg-white">

        <div
            class="mx-auto flex h-[72px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

            {{-- Logo --}}
            <a
                href="{{ route('beranda') }}"
                class="flex items-center gap-2.5"
            >

                <div
                    class="flex h-8 w-8 items-center justify-center rounded-md bg-violet-600"
                >
                    <span
                        class="text-white text-sm"
                        aria-hidden="true"
                    >
                        🔍
                    </span>
                </div>

                <span class="font-extrabold text-slate-900">
                    Campus Find
                </span>

            </a>


            {{-- Kembali ke Beranda --}}
            <a
                href="{{ route('beranda') }}"
                class="flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-violet-600"
            >

                <span>&larr;</span>

                Kembali ke Beranda

            </a>

        </div>

    </header>


    {{-- Form Container --}}
    <main
        class="flex min-h-[calc(100vh-72px)] flex-col items-center justify-center p-4"
    >

        <div
            class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm"
        >

            {{-- Judul --}}
            <div class="mb-8 text-center">

                <h1 class="text-2xl font-bold text-slate-900">
                    Masuk ke Akun Kampus
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Gunakan email institusi atau NIM untuk masuk.
                </p>

            </div>


            {{-- =========================================
                 PILIHAN JENIS AKUN
                 ========================================= --}}

            <div
                class="mb-6 grid grid-cols-2 rounded-lg border border-slate-200 bg-slate-50 p-1"
            >

                {{-- USER --}}
                <button
                    type="button"
                    id="userTab"
                    onclick="switchRole('user')"
                    class="rounded-md bg-violet-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm transition"
                >
                     User
                </button>


                {{-- ADMIN --}}
                <button
                    type="button"
                    id="adminTab"
                    onclick="switchRole('admin')"
                    class="rounded-md px-3 py-2.5 text-sm font-semibold text-slate-500 transition hover:text-violet-600"
                >
                    Administrator
                </button>

            </div>


            {{-- Form Login --}}
            <form
                action="#"
                method="POST"
                class="space-y-5"
            >

                @csrf


                {{-- Menyimpan jenis akun --}}
                <input
                    type="hidden"
                    name="role"
                    id="role"
                    value="user"
                >


                {{-- Email / NIM --}}
                <div>

                    <label
                        for="identifier"
                        id="identifierLabel"
                        class="mb-1 block text-sm font-semibold text-slate-700"
                    >
                        Email Kampus / NIM
                    </label>


                    <input
                        id="identifier"
                        name="identifier"
                        type="text"
                        required
                        placeholder="mahasiswa@kampus.ac.id atau 240511..."
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                    >

                </div>


                {{-- Password --}}
                <div>

                    <label
                        for="password"
                        class="mb-1 block text-sm font-semibold text-slate-700"
                    >
                        Kata Sandi
                    </label>


                    <div class="relative">

                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            placeholder="Masukkan kata sandi"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                        >


                        {{-- Tombol lihat password --}}
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-violet-600"
                        >

                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                />

                            </svg>

                        </button>

                    </div>

                </div>


                {{-- Remember Me & Forgot Password --}}
                <div
                    class="flex items-center justify-between"
                >

                    <label
                        class="flex items-center gap-2"
                    >

                        <input
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500"
                        >

                        <span class="text-sm text-slate-600">
                            Ingat Saya
                        </span>

                    </label>


                    <a
                        href="#"
                        class="text-sm font-semibold text-violet-600 hover:text-violet-700 hover:underline"
                    >
                        Lupa Kata Sandi?
                    </a>

                </div>


                {{-- Submit Button --}}
                <button
                    type="submit"
                    id="loginButton"
                    class="w-full rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2"
                >
                    Masuk
                </button>

            </form>


            {{-- Register --}}
            <div
                id="registerSection"
                class="mt-8 text-center text-sm text-slate-600"
            >

                Belum punya akun?

                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-violet-600 hover:text-violet-700 hover:underline"
                >
                    Daftar Sekarang
                </a>

            </div>

        </div>

    </main>


    {{-- =========================================
         JAVASCRIPT
         HANYA UNTUK PERGANTIAN USER / ADMIN
         ========================================= --}}

    <script>

        function switchRole(role) {

            // Ambil elemen
            const userTab =
                document.getElementById('userTab');

            const adminTab =
                document.getElementById('adminTab');

            const roleInput =
                document.getElementById('role');

            const identifierLabel =
                document.getElementById('identifierLabel');

            const identifierInput =
                document.getElementById('identifier');

            const loginButton =
                document.getElementById('loginButton');

            const registerSection =
                document.getElementById('registerSection');


            // =====================================
            // JIKA USER DIPILIH
            // =====================================

            if (role === 'user') {

                // Menyimpan role
                roleInput.value = 'user';


                // Tab User aktif
                userTab.className =
                    "rounded-md bg-violet-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm transition";


                // Tab Admin tidak aktif
                adminTab.className =
                    "rounded-md px-3 py-2.5 text-sm font-semibold text-slate-500 transition hover:text-violet-600";


                // Label kembali seperti login lama
                identifierLabel.innerText =
                    'Email Kampus / NIM';


                // Placeholder kembali seperti login lama
                identifierInput.placeholder =
                    'mahasiswa@kampus.ac.id atau 240511...';


                // Tombol kembali seperti login lama
                loginButton.innerText =
                    'Masuk';


                // Tampilkan daftar
                registerSection.style.display =
                    'block';

            }


            // =====================================
            // JIKA ADMIN DIPILIH
            // =====================================

            else {

                // Menyimpan role
                roleInput.value = 'admin';


                // Tab Admin aktif
                adminTab.className =
                    "rounded-md bg-violet-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm transition";


                // Tab User tidak aktif
                userTab.className =
                    "rounded-md px-3 py-2.5 text-sm font-semibold text-slate-500 transition hover:text-violet-600";


                // Ubah label
                identifierLabel.innerText =
                    'Email Administrator';


                // Ubah placeholder
                identifierInput.placeholder =
                    'admin@kampus.ac.id';


                // Ubah tombol
                loginButton.innerText =
                    'Masuk sebagai Administrator';


                // Admin tidak perlu daftar
                registerSection.style.display =
                    'none';

            }

        }


        // =========================================
        // SHOW / HIDE PASSWORD
        // =========================================

        function togglePassword() {

            const password =
                document.getElementById('password');


            if (password.type === 'password') {

                password.type = 'text';

            } else {

                password.type = 'password';

            }

        }

    </script>

</body>

</html>