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

        btn.disabled = true;

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
            if (data.is_favorite) {
                btn.classList.add('is-favorite');
                btn.setAttribute('aria-label', 'Quitar de favoritos');
                btn.setAttribute('title', 'Tecnología destacada');
            } else {
                btn.classList.remove('is-favorite');
                btn.setAttribute('aria-label', 'Agregar a favoritos');
                btn.setAttribute('title', 'Marcar como destacada');
            }
        })
        .catch(function (err) {
            showToast(err.message || 'No se pudo alternar favorito', 'error');
        })
        .finally(function () {
            btn.disabled = false;
        });
    });
})();
