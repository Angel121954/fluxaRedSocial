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
const lastFocused = new Map();

const FOCUSABLE_SELECTOR = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled]):not([type="hidden"])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

function getFocusable(modal) {
    return Array.from(modal.querySelectorAll(FOCUSABLE_SELECTOR))
        .filter((node) => node.getClientRects().length > 0);
}

function setAccessibleName(modal) {
    if (modal.hasAttribute('aria-label') || modal.hasAttribute('aria-labelledby')) {
        return;
    }

    const title = modal.querySelector('.modal-title');

    if (!title) {
        return;
    }

    if (!title.id) {
        title.id = `${modal.id}-title`;
    }

    modal.setAttribute('aria-labelledby', title.id);
}

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

    const activeElement = document.activeElement;

    if (activeElement && typeof activeElement.focus === 'function' && activeElement !== document.body) {
        lastFocused.set(id, activeElement);
    }

    openModals.add(id);
    lockBodyScroll();
    el.setAttribute('aria-hidden', 'false');
    setAccessibleName(el);

    requestAnimationFrame(() => {
        if (!openModals.has(id)) {
            return;
        }

        const focusable = getFocusable(el);
        const target = focusable.find((node) => !node.matches('[data-close]'))
            || focusable[0]
            || el.querySelector('.modal-card')
            || el;

        if (!target.matches(FOCUSABLE_SELECTOR)) {
            target.setAttribute('tabindex', '-1');
        }

        target.focus();
    });
}

export function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;

    el.classList.remove('is-open', 'show');
    el.setAttribute('aria-hidden', 'true');

    if (!openModals.delete(id)) {
        return;
    }

    unlockBodyScroll();

    const previous = lastFocused.get(id);
    lastFocused.delete(id);

    if (previous && document.contains(previous) && typeof previous.focus === 'function') {
        previous.focus();
    }
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
            return;
        }

        // Imagen modal (profile/mensajes): cerrar al hacer clic fuera de la imagen
        const imgModal = e.target.closest('.img-modal');
        if (imgModal && e.target === imgModal) {
            closeModal(imgModal.id);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (openModals.size === 0) {
            return;
        }

        const ids = [...openModals];
        const modal = document.getElementById(ids[ids.length - 1]);

        if (!modal) {
            return;
        }

        if (e.key === 'Escape') {
            closeModal(modal.id);
            return;
        }

        if (e.key !== 'Tab') {
            return;
        }

        const focusable = getFocusable(modal);

        if (focusable.length === 0) {
            e.preventDefault();
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (!modal.contains(document.activeElement)) {
            e.preventDefault();
            first.focus();
            return;
        }

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });
});
