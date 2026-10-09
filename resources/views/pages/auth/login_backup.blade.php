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
        <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

            <a href="{{ route('beranda') }}" class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-md bg-violet-600">
                    <span class="text-white text-sm" aria-hidden="true">🔍</span>
                </div>

                <span class="font-extrabold text-slate-900">
                    Campus Find
                </span>
            </a>

            <a href="{{ route('beranda') }}"
               class="flex items-center gap-2 rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 hover:text-violet-600">

                <span>&larr;</span>
                Kembali ke Beranda
            </a>

        </div>
    </header>


    {{-- Form Login --}}
    <main class="flex min-h-[calc(100vh-72px)] flex-col items-center justify-center p-4">

        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">

            {{-- Judul --}}
            <div class="mb-6 text-center">

                <h1 class="text-2xl font-bold text-slate-900">
                    Selamat Datang
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Silakan pilih jenis akun dan masuk ke akun Anda
                </p>

            </div>


            {{-- Pilihan Jenis Akun --}}
            <div class="mb-6 grid grid-cols-2 rounded-lg border border-slate-200 bg-slate-100 p-1">

                {{-- Tombol User --}}
                <button
                    type="button"
                    id="userTab"
                    onclick="switchLogin('user')"
                    class="rounded-md bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition">

                    User

                </button>


                {{-- Tombol Admin --}}
                <button
                    type="button"
                    id="adminTab"
                    onclick="switchLogin('admin')"
                    class="rounded-md px-4 py-2.5 text-sm font-semibold text-slate-500 transition hover:text-violet-600">

                    Administrator

                </button>

            </div>


            {{-- Form --}}
            <form action="#" method="POST" class="space-y-5">

                @csrf

                {{-- Menyimpan jenis akun --}}
                <input
                    type="hidden"
                    id="role"
                    name="role"
                    value="user"
                >


                {{-- Email / NIM --}}
                <div>

                    <label
                        for="identifier"
                        id="identifierLabel"
                        class="mb-1 block text-sm font-semibold text-slate-700">

                        Email Pembeli / NIM

                    </label>

                    <input
                        id="identifier"
                        name="identifier"
                        type="text"
                        required
                        placeholder="mahasiswa@kampus.ac.id atau NIM"

                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                    >

                </div>


                {{-- Password --}}
                <div>

                    <label
                        for="password"
                        class="mb-1 block text-sm font-semibold text-slate-700">

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


                        {{-- Tombol tampilkan password --}}
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-violet-600">

                            👁

                        </button>

                    </div>

                </div>


                {{-- Remember & Forgot Password --}}
                <div class="flex items-center justify-between">

                    <label class="flex items-center gap-2">

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
                        class="text-sm font-semibold text-violet-600 hover:text-violet-700 hover:underline">

                        Lupa Kata Sandi?

                    </a>

                </div>


                {{-- Tombol Login --}}
                <button
                    type="submit"
                    id="loginButton"

                    class="w-full rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2">

                    Masuk sebagai Pembeli

                </button>

            </form>


            {{-- Register User --}}
            <div
                id="registerSection"
                class="mt-8 text-center text-sm text-slate-600">

                Belum punya akun?

                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-violet-600 hover:text-violet-700 hover:underline">

                    Daftar Sekarang

                </a>

            </div>

        </div>

    </main>


    {{-- JavaScript --}}
    <script>

        function switchLogin(type) {

            const userTab = document.getElementById('userTab');
            const adminTab = document.getElementById('adminTab');

            const identifierLabel = document.getElementById('identifierLabel');
            const identifier = document.getElementById('identifier');

            const loginButton = document.getElementById('loginButton');

            const role = document.getElementById('role');

            const registerSection = document.getElementById('registerSection');


            if (type === 'admin') {

                // Menandai bahwa yang dipilih adalah admin
                role.value = 'admin';


                // Tampilan tombol
                adminTab.classList.add(
                    'bg-violet-600',
                    'text-white'
                );

                adminTab.classList.remove(
                    'text-slate-500'
                );


                userTab.classList.remove(
                    'bg-violet-600',
                    'text-white'
                );

                userTab.classList.add(
                    'text-slate-500'
                );


                // Mengubah label
                identifierLabel.innerText = 'Email Administrator';

                identifier.placeholder = 'admin@kampus.ac.id';


                // Mengubah tombol login
                loginButton.innerText = 'Masuk sebagai Administrator';


                // Admin tidak membutuhkan register
                registerSection.style.display = 'none';

            } else {

                // Menandai bahwa yang dipilih adalah user
                role.value = 'user';


                // Tampilan tombol
                userTab.classList.add(
                    'bg-violet-600',
                    'text-white'
                );

                userTab.classList.remove(
                    'text-slate-500'
                );


                adminTab.classList.remove(
                    'bg-violet-600',
                    'text-white'
                );

                adminTab.classList.add(
                    'text-slate-500'
                );


                // Mengubah label
                identifierLabel.innerText = 'Email Pembeli / NIM';

                identifier.placeholder =
                    'mahasiswa@kampus.ac.id atau NIM';


                // Mengubah tombol login
                loginButton.innerText = 'Masuk sebagai Pembeli';


                // Menampilkan kembali register
                registerSection.style.display = 'block';

            }

        }


        // Menampilkan / menyembunyikan password
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