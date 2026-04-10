class ToastManager {
    constructor() {
        this.toasts = [];
        this.defaults = {
            duration: 4000,
            position: 'bottom-center',
            dismissible: true,
            id: null
        };

        this.positions = {
            'top-left': { top: '20px', left: '20px', right: 'auto', bottom: 'auto' },
            'top-center': { top: '20px', left: '50%', right: 'auto', bottom: 'auto', transform: 'translateX(-50%)' },
            'top-right': { top: '20px', right: '20px', left: 'auto', bottom: 'auto' },
            'bottom-left': { bottom: '20px', left: '20px', right: 'auto', top: 'auto' },
            'bottom-center': { bottom: '20px', left: '50%', right: 'auto', top: 'auto', transform: 'translateX(-50%)' },
            'bottom-right': { bottom: '20px', right: '20px', left: 'auto', top: 'auto' }
        };

        this.styles = {
            success: { bg: '#12b3b6' },
            error: { bg: '#ef4444' },
            warning: { bg: '#f59e0b' },
            info: { bg: '#3b82f6' }
        };

        this.createContainer();
    }

    createContainer() {
        if (document.getElementById('toast-container')) return;

        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            z-index: 999999;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }

    getPositionStyles(position) {
        const pos = this.positions[position] || this.positions['bottom-right'];
        return Object.entries(pos)
            .filter(([key]) => key !== 'transform')
            .map(([key, val]) => `${key}: ${val}`)
            .join('; ') + (pos.transform ? `; transform: ${pos.transform}` : '');
    }

    getIcon(type) {
        const icons = {
            check: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
            x: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
            alert: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
            info: `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
        };
        return icons[type];
    }

    createToastElement(options) {
        const style = this.styles[options.type] || this.styles.success;
        const id = options.id || `toast-${Date.now()}`;
        const messageKey = `${options.type}-${options.message}`;

        const toast = document.createElement('div');
        toast.id = id;
        toast.dataset.messageKey = messageKey;
        toast.className = 'toast-entry';
        toast.style.cssText = `
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            background: ${style.bg};
            color: #fff;
            border-radius: var(--r-lg, 14px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-size: 14px;
            font-family: system-ui, -apple-system, sans-serif;
            min-width: 280px;
            max-width: 400px;
            pointer-events: auto;
            animation: toast-in 0.3s ease-out;
        `;

        toast.innerHTML = `
            <div class="toast-content" style="flex: 1;">${options.message}</div>
            ${options.dismissible ? `
            <button class="toast-close" style="
                flex-shrink: 0;
                background: none;
                border: none;
                color: rgba(255,255,255,0.8);
                cursor: pointer;
                padding: 4px;
                display: flex;
                border-radius: 4px;
                transition: background 0.2s;
            ">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            ` : ''}
        `;

        return toast;
    }

    show(message, options = {}) {
        const config = { ...this.defaults, ...options, message };
        const container = document.getElementById('toast-container');
        const messageKey = `${config.type}-${message}`;
        
        const existingToast = this.toasts.find(t => t.messageKey === messageKey);
        if (existingToast) {
            this.updateExisting(existingToast, config.duration);
            return existingToast.id;
        }

        container.style.cssText = `
            position: fixed;
            z-index: 999999;
            display: flex;
            flex-direction: column-reverse;
            gap: 10px;
            pointer-events: none;
            ${this.getPositionStyles(config.position)}
        `;

        const toastEl = this.createToastElement(config);
        container.appendChild(toastEl);

        const toastObj = { 
            id: toastEl.id, 
            element: toastEl, 
            timeout: null,
            messageKey: messageKey
        };
        this.toasts.push(toastObj);

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
        
        el.style.animation = 'none';
        el.offsetHeight;
        el.style.animation = 'toast-pulse 0.2s ease-out';

        if (toastObj.timeout) {
            clearTimeout(toastObj.timeout);
        }
        
        if (duration > 0) {
            toastObj.timeout = setTimeout(() => this.dismiss(toastObj.id), duration);
        }
    }

    dismiss(id) {
        const index = this.toasts.findIndex(t => t.id === id);
        if (index === -1) return;

        const toastObj = this.toasts[index];
        
        if (toastObj.timeout) {
            clearTimeout(toastObj.timeout);
        }

        const el = toastObj.element;
        el.style.animation = 'toast-out 0.2s ease-in forwards';
        
        setTimeout(() => {
            el.remove();
            this.toasts.splice(index, 1);
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
        this.toasts.forEach(t => this.dismiss(t.id));
    }
}

const toast = new ToastManager();

const style = document.createElement('style');
style.textContent = `
    @keyframes toast-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes toast-out {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(20px);
        }
    }
    @keyframes toast-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

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