/* ═══════════════════════════════════════════════════════════
   landing.js — Interacciones de la landing pública.
   Responsabilidades:
     1. Menú móvil (abrir/cerrar con aria, Escape, click fuera).
     2. Reveal de secciones al hacer scroll (IntersectionObserver).
   Sin dependencias. Respetuoso de prefers-reduced-motion.
   ═══════════════════════════════════════════════════════════ */

(function () {
    'use strict';

    /* ─── Menú móvil ──────────────────────────────────────── */
    var btn = document.getElementById('landingMenuBtn');
    var menu = document.getElementById('landingMenu');

    function isMenuOpen() {
        return btn && btn.getAttribute('aria-expanded') === 'true';
    }

    function setMenu(open) {
        if (!btn || !menu) return;

        btn.setAttribute('aria-expanded', String(open));
        btn.setAttribute('aria-label', open ? 'Cerrar menú de navegación' : 'Abrir menú de navegación');
        menu.setAttribute('aria-hidden', String(!open));
        menu.classList.toggle('is-open', open);
    }

    if (btn && menu) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            setMenu(!isMenuOpen());
        });

        document.addEventListener('click', function (e) {
            if (isMenuOpen() && !menu.contains(e.target) && !btn.contains(e.target)) {
                setMenu(false);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isMenuOpen()) {
                setMenu(false);
                btn.focus();
            }
        });

        menu.addEventListener('click', function (e) {
            if (e.target.closest('a')) {
                setMenu(false);
            }
        });
    }

    /* ─── Reveal on scroll ────────────────────────────────── */
    var canAnimate = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var revealEls = document.querySelectorAll('.landing-reveal');

    if (canAnimate || !('IntersectionObserver' in window)) {
        revealEls.forEach(function (el) {
            el.classList.add('is-visible');
        });
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -40px 0px', threshold: 0.08 });

    revealEls.forEach(function (el, index) {
        if (!el.classList.contains('is-visible')) {
            el.style.transitionDelay = Math.min(index * 60, 300) + 'ms';
        }
        observer.observe(el);
    });
})();