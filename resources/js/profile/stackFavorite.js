import { showToast } from '../shared/toast.js';

(function () {
    'use strict';

    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.stack-card-heart[data-tech-id]');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        var techId = btn.getAttribute('data-tech-id');
        if (!techId) return;

        // ── Snapshot estado actual ──
        var wasFavorite = btn.classList.contains('is-favorite');
        var prevAriaLabel = btn.getAttribute('aria-label');

        // ── Optimistic: toggle inmediato ──
        btn.classList.toggle('is-favorite');

        if (wasFavorite) {
            btn.setAttribute('aria-label', 'Agregar a favoritos');
            btn.setAttribute('title', 'Marcar como destacada');
        } else {
            btn.setAttribute('aria-label', 'Quitar de favoritos');
            btn.setAttribute('title', 'Tecnología destacada');
        }

        fetch('/profile/technologies/' + techId + '/favorite', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok) {
                    throw new Error(data.message || 'Error al alternar favorito');
                }
                return data;
            });
        })
        .then(function (data) {
            // ── Confirmar con datos del servidor ──
            btn.classList.toggle('is-favorite', data.is_favorite);
            if (data.is_favorite) {
                btn.setAttribute('aria-label', 'Quitar de favoritos');
                btn.setAttribute('title', 'Tecnología destacada');
            } else {
                btn.setAttribute('aria-label', 'Agregar a favoritos');
                btn.setAttribute('title', 'Marcar como destacada');
            }
        })
        .catch(function (err) {
            // ── Revertir al estado anterior ──
            btn.classList.toggle('is-favorite', wasFavorite);
            btn.setAttribute('aria-label', prevAriaLabel);
            btn.setAttribute('title', wasFavorite ? 'Tecnología destacada' : 'Marcar como destacada');
            showToast(err.message || 'No se pudo alternar favorito', 'error');
        });
    });
})();
