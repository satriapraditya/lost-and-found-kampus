<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Campus Find' }} — Lost &amp; Found Kampus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-text antialiased">
    <x-navbar :active="$active ?? 'beranda'" />

    <main>
        {{ $slot }}
    </main>

    <x-footer />
</body>
</html>
