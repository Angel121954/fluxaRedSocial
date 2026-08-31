/**
 * bottomNavHide.js — Oculta/revela la navegación inferior según el scroll
 * Patrón UX "hide-on-scroll": al bajar se oculta, al subir se revela.
 * Solo aplica en móvil (la barra no se muestra en escritorio).
 */

(() => {
    const HIDE_CLASS = 'bottom-nav--hidden';
    const SCROLL_THRESHOLD = 8;
    const NAV = '.bottom-nav';

    const nav = document.querySelector(NAV);

    if (!nav) {
        return;
    }

    let lastScrollY = window.scrollY;
    let ticking = false;

    function setVisible(visible) {
        nav.classList.toggle(HIDE_CLASS, !visible);
    }

    function handleScroll() {
        const currentY = window.scrollY;
        // No ocultar si estamos en la parte superior de la página
        if (currentY <= SCROLL_THRESHOLD) {
            setVisible(true);
            lastScrollY = currentY;
            ticking = false;
            return;
        }

        const delta = currentY - lastScrollY;

        if (Math.abs(delta) < SCROLL_THRESHOLD) {
            ticking = false;
            return;
        }

        if (delta > 0) {
            setVisible(false); // bajando → ocultar
        } else {
            setVisible(true); // subiendo → revelar
        }

        lastScrollY = currentY;
        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => handleScroll());
            ticking = true;
        }
    }, { passive: true });

    // No ocultar nunca la barra: por defecto visible hasta el primer scroll
    setVisible(true);
})();
