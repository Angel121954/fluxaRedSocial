/**
 * modal.js — Sistema unificado de modales para Fluxa.
 *
 * Proporciona openModal(id) / closeModal(id) globales que:
 *   - manejan la clase .is-open
 *   - gestionan body scroll lock con contador
 *   - cierran al hacer clic en backdrop
 *   - cierran con Escape
 *   - cierran con [data-close="modalId"]
 *
 * Uso desde cualquier JS:
 *   openModal('miModal')
 *   closeModal('miModal')
 *
 * Uso desde HTML:
 *   <button data-close="miModal">Cerrar</button>
 */

import { lockBodyScroll, unlockBodyScroll } from './scrollLock.js';

const openModals = new Set();

export function openModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    if (openModals.has(id)) return;

    if (el.classList.contains('modal-backdrop')) {
        el.classList.add('is-open');
    } else if (el.classList.contains('img-modal')) {
        el.classList.add('show');
    } else {
        return;
    }

    openModals.add(id);
    lockBodyScroll();
}

export function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('is-open', 'show');
    openModals.delete(id);
    unlockBodyScroll();
}

window.openModal = openModal;
window.closeModal = closeModal;

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const closeBtn = e.target.closest('[data-close]');
        if (closeBtn) {
            closeModal(closeBtn.dataset.close);
            return;
        }

        const backdrop = e.target.closest('.modal-backdrop');
        if (backdrop && e.target === backdrop) {
            closeModal(backdrop.id);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape' || openModals.size === 0) return;
        const ids = [...openModals];
        closeModal(ids[ids.length - 1]);
    });
});
