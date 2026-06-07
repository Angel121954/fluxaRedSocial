export function formatTime(isoString) {
    const d = new Date(isoString);
    return d.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'America/Bogota' });
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

export function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '';
    const units = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    const size = (bytes / Math.pow(1024, i)).toFixed(i > 0 ? 1 : 0);
    return size + ' ' + units[i];
}