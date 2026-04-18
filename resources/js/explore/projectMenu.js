/**
 * explore/projectMenu.js — Dropdown de acciones por proyecto
 */

import { showToast } from '../shared/toast.js';

// ── Abrir / cerrar dropdown ────────────────────────────────────────────────
document.addEventListener('click', (e) => {
    const menuBtn = e.target.closest('.post-menu-btn');
    const dropItem = e.target.closest('.drop-item');

    if (menuBtn) {
        e.preventDefault();
        e.stopPropagation();

        const projectId = menuBtn.dataset.projectId;
        const dropdown = document.querySelector(`.drop-menu[data-project-id="${projectId}"]`);

        // Cerrar otros dropdowns abiertos
        document.querySelectorAll('.drop-menu').forEach(d => {
            if (d !== dropdown) d.classList.remove('open');
        });
        document.querySelectorAll('.post-menu-btn').forEach(b => {
            if (b !== menuBtn) b.classList.remove('is-open');
        });

        const isOpen = dropdown?.classList.toggle('open');
        menuBtn.classList.toggle('is-open', isOpen);
        return;
    }

    if (dropItem) {
        const { action, projectId } = dropItem.dataset;

        // Solo interceptar drop-items de proyectos con action definida
        // Los <a href> del perfil (Configuración, Descargar CV) no tienen action
        if (!action) return;

        e.preventDefault();

        handleProjectAction(action, projectId, dropItem, () => {
            dropItem.closest('.drop-menu')?.classList.remove('open');
            document.querySelector(`.post-menu-btn[data-project-id="${projectId}"]`)
                ?.classList.remove('is-open');
        });
        return;
    }

    // Click fuera → cerrar todos
    document.querySelectorAll('.drop-menu.open').forEach(d => d.classList.remove('open'));
    document.querySelectorAll('.post-menu-btn.is-open').forEach(b => b.classList.remove('is-open'));
});

// Cerrar con Escape
document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.drop-menu.open').forEach(d => d.classList.remove('open'));
    document.querySelectorAll('.post-menu-btn.is-open').forEach(b => b.classList.remove('is-open'));
    document.querySelectorAll('.modal-backdrop.show').forEach(m => m.classList.remove('show'));
});

// ── Lógica de acciones ────────────────────────────────────────────────────
function handleProjectAction(action, projectId, dropItem, closeMenu) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const url = `${window.location.origin}/projects/${projectId}`;

    switch (action) {
        case 'bookmark': {
            const span = dropItem.querySelector('span');
            const isBookmarked = span.textContent.includes('Quitar');
            span.textContent = isBookmarked ? 'Agregar a favoritos' : 'Quitar de favoritos';
            closeMenu?.();

            fetch(`/projects/${projectId}/bookmark`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
            })
                .then(res => res.json())
                .then(data => {
                    span.textContent = data.is_bookmarked ? 'Quitar de favoritos' : 'Agregar a favoritos';
                })
                .catch(() => { span.textContent = isBookmarked ? 'Quitar de favoritos' : 'Agregar a favoritos'; });
            break;
        }

        case 'share':
        case 'copy-link':
            closeMenu?.();
            if (navigator.share) {
                navigator.share({ url });
            } else {
                navigator.clipboard.writeText(url);
                showToast('Enlace copiado');
            }
            break;

        case 'report':
            closeMenu?.();
            document.getElementById('reportModal')?.classList.add('show');
            if (document.getElementById('reportForm')) {
                document.getElementById('reportForm').dataset.projectId = projectId;
                document.getElementById('reportReason').value = '';
            }
            break;
    }
}

// ── Cerrar modales genéricos ──────────────────────────────────────────────
document.addEventListener('click', (e) => {
    const closeBtn = e.target.closest('[data-close]');
    if (closeBtn) {
        document.getElementById(closeBtn.dataset.close)?.classList.remove('show');
        return;
    }
    if (e.target.classList.contains('modal-backdrop')) {
        e.target.classList.remove('show');
    }
});

// ── Submit de reporte ─────────────────────────────────────────────────────
document.getElementById('reportForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const reason = document.getElementById('reportReason').value;
    if (reason.length < 10) return;

    fetch(`/projects/${this.dataset.projectId}/report`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ reason }),
    })
        .then(res => res.json())
        .then(data => {
            document.getElementById('reportModal')?.classList.remove('show');
            showToast(data.message || 'Reporte enviado');
        });
});
