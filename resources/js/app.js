import './bootstrap';
import { initThemeToggle } from './theme';

initThemeToggle();

/* ==========================================================
   Menu ☰ di navbar (layar kecil)
   ========================================================== */
const menuToggle = document.querySelector('[data-mobile-menu-toggle]');
const mobileMenu = document.getElementById('mobile-menu');

function setMobileMenu(open) {
    mobileMenu.hidden = !open;
    menuToggle.setAttribute('aria-expanded', String(open));
    menuToggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    menuToggle.querySelector('[data-icon-open]').classList.toggle('hidden', open);
    menuToggle.querySelector('[data-icon-close]').classList.toggle('hidden', !open);
}

if (menuToggle && mobileMenu) {
    menuToggle.addEventListener('click', () => setMobileMenu(mobileMenu.hidden));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !mobileMenu.hidden) {
            setMobileMenu(false);
            menuToggle.focus();
        }
    });

    // Tutup otomatis kalau layar dilebarkan sampai menu desktop muncul
    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches) setMobileMenu(false);
    });
}
