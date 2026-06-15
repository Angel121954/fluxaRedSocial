/**
 * projectMedia.js · Fluxa
 * Lightbox para pm-grid — sin dependencias externas
 * Tolerante a que el DOM del lightbox se inyecte después de cargar el script
 * (ej. navegación AJAX entre tabs de explore).
 */

(function () {
    'use strict';

    /* ── Helper para obtener elementos del lightbox ───────── */
    const $id = (id) => document.getElementById(id);

    let images = [];
    let current = 0;

    /* ── Recopilar imágenes del grid ──────────────────────── */
    function collectImages(grid) {
        const urls = JSON.parse(grid.dataset.all || '[]');
        const buttons = grid.querySelectorAll('.pm-item[data-lightbox]');
        return urls.map((url, i) => ({
            url,
            alt: buttons[i]?.querySelector('img')?.alt || `Imagen ${i + 1}`,
        }));
    }

    /* ── Abrir ────────────────────────────────────────────── */
    function open(grid, idx) {
        const lb = $id('pmLightbox');
        if (!lb) return;

        images = collectImages(grid);
        current = idx;
        lb.removeAttribute('hidden');
        lockBodyScroll();
        loadImage(current);
    }

    /* ── Cargar imagen ────────────────────────────────────── */
    function loadImage(idx) {
        const item = images[idx];
        if (!item) return;

        const lb = $id('pmLightbox');
        const lbImg = $id('pmLbImg');
        const lbCount = $id('pmLbCounter');
        const lbSpin = $id('pmLbSpinner');
        const lbPrev = $id('pmLbPrev');
        const lbNext = $id('pmLbNext');
        if (!lb || !lbImg || !lbCount || !lbSpin || !lbPrev || !lbNext) return;

        lbSpin.classList.add('loading');
        lbImg.style.opacity = '0';

        const tmp = new Image();
        tmp.onload = () => { lbImg.src = item.url; lbImg.alt = item.alt; lbImg.style.opacity = '1'; lbSpin.classList.remove('loading'); };
        tmp.onerror = () => { lbImg.src = item.url; lbImg.style.opacity = '1'; lbSpin.classList.remove('loading'); };
        tmp.src = item.url;

        current = idx;
        lbCount.textContent = `${idx + 1} / ${images.length}`;
        lbPrev.disabled = idx === 0;
        lbNext.disabled = idx === images.length - 1;
        lbPrev.style.display = images.length < 2 ? 'none' : '';
        lbNext.style.display = images.length < 2 ? 'none' : '';
        lbCount.style.display = images.length < 2 ? 'none' : '';
    }

    /* ── Cerrar ───────────────────────────────────────────── */
    function close() {
        const lb = $id('pmLightbox');
        const lbImg = $id('pmLbImg');
        if (!lb) return;

        lb.setAttribute('hidden', '');
        unlockBodyScroll();
        images = [];
        current = 0;
        if (lbImg) lbImg.src = '';
    }

    /* ── Event delegation — un solo listener para todo ────── */
    document.addEventListener('click', (e) => {
        if (!(e.target instanceof Element)) return;

        // Click en tile del grid → abrir lightbox
        const tile = e.target.closest('.pm-item[data-lightbox]');
        if (tile) {
            const grid = tile.closest('.pm-grid');
            if (grid) {
                open(grid, parseInt(tile.dataset.index ?? '0', 10));
            }
            return;
        }

        // Cerrar
        if (e.target.closest('#pmLbClose')) {
            close();
            return;
        }

        // Anterior
        if (e.target.closest('#pmLbPrev')) {
            if (current > 0) loadImage(current - 1);
            return;
        }

        // Siguiente
        if (e.target.closest('#pmLbNext')) {
            if (current < images.length - 1) loadImage(current + 1);
            return;
        }

        // Click en el fondo del lightbox
        const lb = $id('pmLightbox');
        if (lb && e.target === lb) {
            close();
        }
    });

    /* ── Teclado ──────────────────────────────────────────── */
    document.addEventListener('keydown', (e) => {
        const lb = $id('pmLightbox');
        if (!lb || lb.hasAttribute('hidden')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft' && current > 0) loadImage(current - 1);
        if (e.key === 'ArrowRight' && current < images.length - 1) loadImage(current + 1);
    });

    /* ── Swipe táctil ─────────────────────────────────────── */
    let touchStartX = 0;
    document.addEventListener('touchstart', (e) => {
        const lb = $id('pmLightbox');
        if (!lb || lb.hasAttribute('hidden')) return;
        touchStartX = e.changedTouches[0].clientX;
    }, { passive: true });

    document.addEventListener('touchend', (e) => {
        const lb = $id('pmLightbox');
        if (!lb || lb.hasAttribute('hidden')) return;
        const dx = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(dx) < 50) return;
        if (dx < 0 && current < images.length - 1) loadImage(current + 1);
        if (dx > 0 && current > 0) loadImage(current - 1);
    }, { passive: true });

    /* ── Hover en video ───────────────────────────────────── */
    document.addEventListener('mouseenter', (e) => {
        if (!(e.target instanceof Element)) return;
        const btn = e.target.closest('.pm-item');
        if (!btn) return;
        const video = btn.querySelector('video.pm-media');
        if (video) video.play().catch(() => { });
    }, true);

    document.addEventListener('mouseleave', (e) => {
        if (!(e.target instanceof Element)) return;
        const btn = e.target.closest('.pm-item');
        if (!btn) return;
        const video = btn.querySelector('video.pm-media');
        if (video) { video.pause(); video.currentTime = 0; }
    }, true);

})();