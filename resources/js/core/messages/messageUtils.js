export function formatTime(isoString) {
    const d = new Date(isoString);
    return d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false });
}

export function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str ?? '')));
    return div.innerHTML;
}

export function scrollToBottom(bubbleList, smooth = false) {
    if (!bubbleList) return;
    bubbleList.scrollTo({
        top: bubbleList.scrollHeight,
        behavior: smooth ? 'smooth' : 'instant',
    });
}

export function autosizeInput(input) {
    if (!input) return;
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 140) + 'px';
}