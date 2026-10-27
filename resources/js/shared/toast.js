import { escapeHtml } from './escapeHtml.js';

class ToastManager {
    constructor() {
        this.toasts = [];
        this.defaults = {
            duration: 4000,
            position: 'bottom-right',
            dismissible: true,
            id: null,
        };

        this.createContainer();
    }

    createContainer() {
        if (document.getElementById('toast-container')) return;

        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        container.dataset.position = this.defaults.position;
        document.body.appendChild(container);
    }

    getIcon(type) {
        const icons = {
            success: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
            error: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
            warning: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
            info: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        };

        return icons[type] || icons.info;
    }

    createToastElement(options) {
        const type = options.type || 'info';
        const id = options.id || `toast-${Date.now()}`;
        const messageKey = `${type}-${options.message}`;

        const toast = document.createElement('div');
        toast.id = id;
        toast.dataset.messageKey = messageKey;
        toast.className = `toast-entry toast-entry--${type}`;
        toast.setAttribute('role', type === 'error' || type === 'warning' ? 'alert' : 'status');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <span class="toast-entry__icon" aria-hidden="true">${this.getIcon(type)}</span>
            <div class="toast-content">${escapeHtml(options.message)}</div>
            ${options.dismissible ? `
            <button type="button" class="toast-close" aria-label="Cerrar notificación">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            ` : ''}
        `;

        return toast;
    }

    show(message, options = {}) {
        const config = { ...this.defaults, ...options, message };
        const type = config.type || 'info';
        const container = document.getElementById('toast-container');
        const messageKey = `${type}-${message}`;

        const existingToast = this.toasts.find((t) => !t.dismissing && t.messageKey === messageKey);
        if (existingToast) {
            this.updateExisting(existingToast, config.duration);
            return existingToast.id;
        }

        container.dataset.position = config.position;

        const toastEl = this.createToastElement({ ...config, type });
        container.appendChild(toastEl);

        const toastObj = {
            id: toastEl.id,
            element: toastEl,
            timeout: null,
            messageKey,
            dismissing: false,
        };

        this.toasts.push(toastObj);

        const pause = () => {
            if (toastObj.timeout) {
                clearTimeout(toastObj.timeout);
                toastObj.timeout = null;
            }
        };

        const resume = () => {
            if (!toastObj.dismissing && config.duration > 0) {
                toastObj.timeout = setTimeout(() => this.dismiss(toastObj.id), config.duration);
            }
        };

        toastEl.addEventListener('mouseenter', pause);
        toastEl.addEventListener('mouseleave', resume);
        toastEl.addEventListener('focusin', pause);
        toastEl.addEventListener('focusout', resume);

        if (config.dismissible) {
            const closeBtn = toastEl.querySelector('.toast-close');
            closeBtn?.addEventListener('click', () => this.dismiss(toastObj.id));
        }

        if (config.duration > 0) {
            toastObj.timeout = setTimeout(() => this.dismiss(toastObj.id), config.duration);
        }

        return toastObj.id;
    }

    updateExisting(toastObj, duration) {
        const el = toastObj.element;

        el.classList.remove('toast-entry--pulse');
        void el.offsetWidth;
        el.classList.add('toast-entry--pulse');

        if (toastObj.timeout) {
            clearTimeout(toastObj.timeout);
        }

        if (duration > 0) {
            toastObj.timeout = setTimeout(() => this.dismiss(toastObj.id), duration);
        }
    }

    dismiss(id) {
        const toastObj = this.toasts.find((t) => t.id === id);
        if (!toastObj || toastObj.dismissing) return;

        toastObj.dismissing = true;

        if (toastObj.timeout) {
            clearTimeout(toastObj.timeout);
            toastObj.timeout = null;
        }

        const el = toastObj.element;
        el.classList.add('toast-entry--out');

        setTimeout(() => {
            el.remove();

            const index = this.toasts.findIndex((t) => t.id === id);
            if (index !== -1) {
                this.toasts.splice(index, 1);
            }
        }, 200);
    }

    success(message, options = {}) {
        return this.show(message, { ...options, type: 'success' });
    }

    error(message, options = {}) {
        return this.show(message, { ...options, type: 'error', duration: 5000 });
    }

    warning(message, options = {}) {
        return this.show(message, { ...options, type: 'warning' });
    }

    info(message, options = {}) {
        return this.show(message, { ...options, type: 'info' });
    }

    dismissAll() {
        this.toasts.forEach((t) => this.dismiss(t.id));
    }
}

const toast = new ToastManager();

export function showToast(message, type = 'success', options = {}) {
    return toast.show(message, { ...options, type });
}

export function initSessionToast() {
    const toastData = window.sessionToast;
    if (toastData) {
        toast.show(toastData.message, { type: toastData.type });
    }
}

window.addEventListener('DOMContentLoaded', () => {
    initSessionToast();
    window.toast = toast;
});

window.showToast = showToast;
