import { escapeHtml, formatTime } from './messageUtils.js';

function createDateSeparator(dateStr = null) {
    const date = dateStr ? new Date(dateStr) : new Date();
    const today = new Date();
    const isToday = date.toDateString() === today.toDateString();

    const label = isToday ? 'Hoy' : date.toLocaleDateString('es-CO', { day: 'numeric', month: 'short', year: 'numeric' });

    const sep = document.createElement('div');
    sep.className = 'msgs-date-separator';
    sep.innerHTML = `<span>${label}</span>`;
    return sep;
}

export function ensureDateSeparator(bubbleList) {
    const existing = bubbleList.querySelector('.msgs-date-separator');
    if (!existing) {
        bubbleList.appendChild(createDateSeparator());
    }
}

export function createOwnBubble(text, status = '') {
    const now = new Date();
    const time = now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false });

    const wrap = document.createElement('div');
    wrap.className = 'msgs-bubble-wrap mine';

    const bubble = document.createElement('div');
    bubble.className = 'msgs-bubble msgs-bubble-mine';
    bubble.textContent = text;

    const timeSpan = document.createElement('span');
    timeSpan.className = `msgs-bubble-time${status ? ' ' + status : ''}`;
    timeSpan.innerHTML = `
        ${time}
        <svg class="msgs-sent-check" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    `;

    bubble.appendChild(timeSpan);
    wrap.appendChild(bubble);
    return wrap;
}

export function appendReceivedBubble(msg, bubbleList) {
    if (!bubbleList) return;

    ensureDateSeparator(bubbleList);

    const wrap = document.createElement('div');
    wrap.className = 'msgs-bubble-wrap theirs';

    wrap.innerHTML = `
        <img src="${escapeHtml(msg.sender?.avatar_url)}"
             alt="" class="msgs-bubble-avatar"
             onerror="this.src='/img/default-avatar.png'">
        <div class="msgs-bubble msgs-bubble-theirs">
            ${escapeHtml(msg.body)}
            <span class="msgs-bubble-time">
                ${formatTime(msg.created_at)}
            </span>
        </div>
    `;

    bubbleList.appendChild(wrap);
}

export function renderModalResults(users, modalResults) {
    if (!modalResults) return;

    if (!users.length) {
        modalResults.innerHTML = '<p class="msgs-modal-hint">No se encontraron usuarios.</p>';
        return;
    }

    modalResults.innerHTML = '';

    users.forEach((user) => {
        const item = document.createElement('a');
        item.href = `/messages/chat/${user.username}`;
        item.className = 'msgs-modal-user-item';
        item.setAttribute('role', 'option');

        item.innerHTML = `
            <img src="${escapeHtml(user.avatar_url)}"
                alt="${escapeHtml(user.name)}"
                class="msgs-modal-user-avatar"
                onerror="this.src='/img/default-avatar.png'">
            <div class="msgs-modal-user-info">
                <div class="msgs-modal-user-name">${escapeHtml(user.name)}</div>
                <div class="msgs-modal-user-handle">@${escapeHtml(user.username)}</div>
            </div>
        `;

        modalResults.appendChild(item);
    });
}

export function updateBubbleStatus(bubble, status) {
    const time = bubble.querySelector('.msgs-bubble-time');
    if (!time) return;

    time.classList.remove('sending');

    if (status === 'error') {
        bubble.classList.add('msgs-bubble-failed');
        time.textContent = 'Error al enviar';
    }
}

export function markConvAsUnread(convId, convList) {
    const convItem = convList?.querySelector(`[href*="conv=${convId}"]`);
    if (!convItem) return;

    if (!convItem.querySelector('.msgs-unread-badge')) {
        const badge = document.createElement('span');
        badge.className = 'msgs-unread-badge';
        badge.textContent = '1';
        const rowBottom = convItem.querySelector('.msgs-conv-row-bottom');
        if (rowBottom) rowBottom.appendChild(badge);
    }
}