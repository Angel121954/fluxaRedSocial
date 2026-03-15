/**
 * projectMedia.js · Fluxa
 * Lightbox para pm-grid — sin dependencias externas
 */

(function () {
    'use strict';

    const lb = document.getElementById('pmLightbox');
    const lbImg = document.getElementById('pmLbImg');
    const lbClose = document.getElementById('pmLbClose');
    const lbPrev = document.getElementById('pmLbPrev');
    const lbNext = document.getElementById('pmLbNext');
    const lbCount = document.getElementById('pmLbCounter');
    const lbSpin = document.getElementById('pmLbSpinner');

    if (!lb) return;

    let images = [];
    let current = 0;

    /* ── Recopilar imágenes del grid ──────────────────────── */
    function collectImages(grid) {
        return Array.from(grid.querySelectorAll('.pm-item[data-lightbox]'))
            .map((btn, i) => ({
                url: btn.dataset.lightbox,
                alt: btn.querySelector('img')?.alt || `Imagen ${i + 1}`,
            }));
    }

    /* ── Abrir ────────────────────────────────────────────── */
    function open(grid, idx) {
        images = collectImages(grid);
        current = idx;
        lb.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        loadImage(current);
    }

    /* ── Cargar imagen ────────────────────────────────────── */
    function loadImage(idx) {
        const item = images[idx];
        if (!item) return;

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
        lb.setAttribute('hidden', '');
        document.body.style.overflow = '';
        images = [];
        current = 0;
        lbImg.src = '';
    }

    /* ── Click en tile ────────────────────────────────────── */
    document.addEventListener('click', (e) => {
        if (!(e.target instanceof Element)) return;          // ← fix
        const btn = e.target.closest('.pm-item[data-lightbox]');
        if (!btn) return;
        const grid = btn.closest('.pm-grid');
        if (!grid) return;
        open(grid, parseInt(btn.dataset.index ?? '0', 10));
    });

    /* ── Controles lightbox ───────────────────────────────── */
    lbClose.addEventListener('click', close);
    lbPrev.addEventListener('click', () => { if (current > 0) loadImage(current - 1); });
    lbNext.addEventListener('click', () => { if (current < images.length - 1) loadImage(current + 1); });
    lb.addEventListener('click', (e) => { if (e.target === lb) close(); });

    /* ── Teclado ──────────────────────────────────────────── */
    document.addEventListener('keydown', (e) => {
        if (lb.hasAttribute('hidden')) return;
        if (e.key === 'Escape') close();
        if (e.key === 'ArrowLeft' && current > 0) loadImage(current - 1);
        if (e.key === 'ArrowRight' && current < images.length - 1) loadImage(current + 1);
    });

    /* ── Swipe táctil ─────────────────────────────────────── */
    let touchStartX = 0;
    lb.addEventListener('touchstart', (e) => { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', (e) => {
        const dx = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(dx) < 50) return;
        if (dx < 0 && current < images.length - 1) loadImage(current + 1);
        if (dx > 0 && current > 0) loadImage(current - 1);
    }, { passive: true });

    /* ── Hover en video ───────────────────────────────────── */
    document.addEventListener('mouseenter', (e) => {
        if (!(e.target instanceof Element)) return;          // ← fix
        const btn = e.target.closest('.pm-item');
        if (!btn) return;
        const video = btn.querySelector('video.pm-media');
        if (video) video.play().catch(() => { });
    }, true);

    document.addEventListener('mouseleave', (e) => {
        if (!(e.target instanceof Element)) return;          // ← fix
        const btn = e.target.closest('.pm-item');
        if (!btn) return;
        const video = btn.querySelector('video.pm-media');
        if (video) { video.pause(); video.currentTime = 0; }
    }, true);

})();