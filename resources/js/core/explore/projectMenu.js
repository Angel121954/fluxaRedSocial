/**
 * explore/projectMenu.js — Dropdown de acciones por proyecto
 */

import { showToast } from '../../shared/toast.js';

// ── Abrir / cerrar dropdown ────────────────────────────────────────────────
document.addEventListener('click', (e) => {
    const menuBtn = e.target.closest('.post-menu-btn, .msn-card-menu');
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
        document.querySelectorAll('.post-menu-btn, .msn-card-menu').forEach(b => {
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
            document.querySelector(`.post-menu-btn[data-project-id="${projectId}"], .msn-card-menu[data-project-id="${projectId}"]`)
                ?.classList.remove('is-open');
        });
        return;
    }

    // Click fuera → cerrar todos
    document.querySelectorAll('.drop-menu.open').forEach(d => d.classList.remove('open'));
    document.querySelectorAll('.post-menu-btn.is-open, .msn-card-menu.is-open').forEach(b => b.classList.remove('is-open'));
});

// Cerrar con Escape
document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.drop-menu.open').forEach(d => d.classList.remove('open'));
    document.querySelectorAll('.post-menu-btn.is-open, .msn-card-menu.is-open').forEach(b => b.classList.remove('is-open'));
    const hadOpenModals = document.querySelectorAll('.modal-backdrop.show').length > 0;
    document.querySelectorAll('.modal-backdrop.show').forEach(m => m.classList.remove('show'));
    if (hadOpenModals) unlockBodyScroll();
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

            dropItem.style.pointerEvents = 'none';
            dropItem.style.opacity = '0.5';

            fetch(`/projects/${projectId}/bookmark`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
            })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    span.textContent = data.is_bookmarked ? 'Quitar de favoritos' : 'Agregar a favoritos';
                })
                .catch(() => {
                    span.textContent = isBookmarked ? 'Quitar de favoritos' : 'Agregar a favoritos';
                    showToast('No se pudo actualizar el favorito', 'error');
                })
                .finally(() => {
                    dropItem.style.pointerEvents = '';
                    dropItem.style.opacity = '';
                });
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

        case 'edit':
            closeMenu?.();
            if (typeof window.abrirEditModal === 'function') {
                window.abrirEditModal(projectId);
            }
            break;

        case 'delete':
            const card = dropItem.closest('.post-card, .msn-card');
            const title = card?.querySelector('.project-title, .msn-card-title')?.textContent?.trim() ?? 'este proyecto';
            closeMenu?.();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Eliminar proyecto?',
                    text: 'Se eliminará "' + title + '" permanentemente.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    deleteProject(projectId, card);
                });
            } else {
                if (confirm('¿Eliminar "' + title + '" permanentemente?')) {
                    deleteProject(projectId, card);
                }
            }
            break;

        case 'report':
            closeMenu?.();
            const reportForm = document.getElementById('reportForm');
            const reportTitle = document.getElementById('reportModalTitle');
            const reportDesc = document.getElementById('reportModalDesc');
            if (reportTitle) reportTitle.textContent = 'Reportar proyecto';
            if (reportDesc) reportDesc.textContent = '¿Por qué quieres reportar este proyecto?';
            if (reportForm) {
                reportForm.dataset.projectId = projectId;
                reportForm.removeAttribute('data-user-id');
                reportForm.dataset.type = 'project';
            }
            const reasonField = document.getElementById('reportReason');
            if (reasonField) reasonField.value = '';
            const modal = document.getElementById('reportModal');
            if (modal) {
                modal.classList.add('show');
                lockBodyScroll();
            }
            break;
    }
}

// ── Eliminar proyecto ──────────────────────────────────────────────────────
function deleteProject(projectId, card) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    fetch('/projects/' + projectId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_method=DELETE',
    })
        .then(function (res) {
            if (!res.ok) throw new Error();
            return res.json();
        })
        .then(function (data) {
            showToast(data.message || 'Proyecto eliminado', 'success');
            card.style.transition = 'opacity 0.3s, transform 0.3s';
            card.style.opacity = '0';
            card.style.transform = 'translateX(20px)';
            setTimeout(function () { card.remove(); }, 300);
        })
        .catch(function () {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error', 'No se pudo eliminar el proyecto.', 'error');
            } else {
                alert('No se pudo eliminar el proyecto.');
            }
        });
}

// ── Cerrar modales genéricos ──────────────────────────────────────────────
document.addEventListener('click', (e) => {
    const closeBtn = e.target.closest('[data-close]');
    if (closeBtn) {
        const modal = document.getElementById(closeBtn.dataset.close);
        if (modal) {
            const wasOpen = modal.classList.contains('show');
            modal.classList.remove('show');
            if (wasOpen) unlockBodyScroll();
        }
        return;
    }
    if (e.target.classList.contains('modal-backdrop')) {
        const wasOpen = e.target.classList.contains('show');
        e.target.classList.remove('show');
        if (wasOpen) unlockBodyScroll();
    }
});

// ── Submit de reporte ─────────────────────────────────────────────────────
document.getElementById('reportForm')?.addEventListener('submit', function (e) {
    e.preventDefault();
    const reason = document.getElementById('reportReason').value;
    if (reason.length < 10) return;

    const type = this.dataset.type || 'project';
    let url;
    if (type === 'user' && this.dataset.userId) {
        url = `/users/${this.dataset.userId}/report`;
    } else {
        url = `/projects/${this.dataset.projectId}/report`;
    }

    fetch(url, {
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
