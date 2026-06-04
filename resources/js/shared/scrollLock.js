/**
 * scrollLock.js — Control de scroll del body al abrir/cerrar modales.
 *
 * Usa un contador para soportar múltiples modales abiertos simultáneamente.
 * Calcula el ancho del scrollbar una vez y compensa con paddingRight
 * para evitar layout shift al ocultar el scroll.
 *
 * Uso desde cualquier modal:
 *   lockBodyScroll()   // al abrir
 *   unlockBodyScroll() // al cerrar
 */

let openCount = 0;

let scrollbarWidth = null;

function getScrollbarWidth() {
    if (scrollbarWidth !== null) return scrollbarWidth;
    const el = document.createElement('div');
    el.style.cssText = 'width:50px;height:50px;overflow-y:scroll;position:absolute;top:-9999px';
    document.body.appendChild(el);
    scrollbarWidth = el.offsetWidth - el.clientWidth;
    document.body.removeChild(el);
    return scrollbarWidth;
}

function shouldReserveScrollbarSpace() {
    return window.innerWidth > 768 && getScrollbarWidth() > 0;
}

export function lockBodyScroll() {
    openCount++;
    if (openCount === 1) {
        document.body.style.overflow = 'hidden';
        if (shouldReserveScrollbarSpace()) {
            document.body.style.paddingRight = getScrollbarWidth() + 'px';
        }
    }
}

export function unlockBodyScroll() {
    openCount = Math.max(0, openCount - 1);
    if (openCount === 0) {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
}

window.lockBodyScroll = lockBodyScroll;
window.unlockBodyScroll = unlockBodyScroll;
