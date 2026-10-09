{{--
    Pasang tema sebelum halaman digambar supaya tidak berkedip putih.
    Taruh di <head> sebelum @vite. Pilihan tersimpan di localStorage
    (kunci sama dengan resources/js/theme.js); kalau belum pernah memilih,
    ikut pengaturan sistem.
--}}
<script>
    (function () {
        var theme = null;
        try { theme = localStorage.getItem('theme'); } catch (e) {}
        if (theme !== 'light' && theme !== 'dark') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.dataset.theme = theme;
    })();
</script>
