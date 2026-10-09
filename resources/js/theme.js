/* ==========================================================
   Mode gelap / terang — dipakai situs publik & area admin.
   Tema awal sudah dipasang oleh <x-theme-script /> di <head>,
   file ini hanya mengurus tombol [data-theme-toggle].
   Pilihan disimpan dengan kunci yang sama, jadi tema di situs
   publik dan admin selalu sama.
   ========================================================== */
export const THEME_STORAGE_KEY = 'theme';

export const currentTheme = () => (document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');

/**
 * Pasang klik di semua tombol [data-theme-toggle].
 * onChange(theme) dipanggil setelah tema berganti, misal untuk menggambar ulang grafik.
 */
export function initThemeToggle(onChange) {
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = currentTheme() === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = next;

            try {
                localStorage.setItem(THEME_STORAGE_KEY, next);
            } catch (e) {
                // Mode privat / penyimpanan diblokir: tema tetap berganti, hanya tidak diingat
            }

            onChange?.(next);
        });
    });
}
