import { escapeHtml, formatTime } from './messageUtils.js';

function getDateLabel(dateStr) {
    const msgDateStr = dateStr.split('T')[0];

    const now = new Date();
    const todayStr = now.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    const yesterday = new Date(now);
    yesterday.setDate(yesterday.getDate() - 1);
    const yesterdayStr = yesterday.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    if (msgDateStr === todayStr) {
        return 'Hoy';
    }
    if (msgDateStr === yesterdayStr) {
        return 'Ayer';
    }

    const msgDate = new Date(msgDateStr + 'T12:00:00');
    return msgDate.toLocaleDateString('es-CO', { day: 'numeric', month: 'long', year: 'numeric' });
}

function createDateSeparator(dateStr = null) {
    const label = getDateLabel(dateStr);
    const sep = document.createElement('div');
    sep.className = 'msgs-date-separator';
    sep.dataset.date = dateStr || new Date().toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });
    sep.innerHTML = `<span>${label}</span>`;
    return sep;
}

export function ensureDateSeparator(bubbleList, messageDateStr = null) {
    const dateToCheck = messageDateStr || new Date().toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    const existingSeparators = bubbleList.querySelectorAll('.msgs-date-separator');
    for (let sep of existingSeparators) {
        if (sep.dataset.date === dateToCheck) {
            return false;
        }
    }

    const sep = createDateSeparator(dateToCheck);
    bubbleList.appendChild(sep);
    return true;
}

export function createOwnBubble(text, status = '', dateStr = null) {
    const now = dateStr ? new Date(dateStr) : new Date();
    const time = now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'America/Bogota' });
    const dateKey = now.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    const wrap = document.createElement('div');
    wrap.className = 'msgs-bubble-wrap mine';
    wrap.dataset.date = dateKey;

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

    const msgDate = msg.created_at
        ? msg.created_at.split('T')[0]
        : new Date().toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    const needsSeparator = ensureDateSeparator(bubbleList, msgDate);

    const wrap = document.createElement('div');
    wrap.className = 'msgs-bubble-wrap theirs';
    wrap.dataset.date = msgDate;

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

export function showNotAcceptingMessages(bubbleList) {
    if (!bubbleList) return;
    
    const existing = bubbleList.querySelector('.msgs-not-accepting');
    if (existing) return;

    const notice = document.createElement('div');
    notice.className = 'msgs-not-accepting';
    notice.innerHTML = `
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M18.364 5.636a9 9 0 010 12.728M5.636 5.636a9 9 0 0112.728 0M12 2v4m0 12v4m-7.07-15.07l2.829 2.828m8.485 8.485l2.828 2.828M2 12h4m12 0h4" />
        </svg>
        <span>Este usuario no acepta mensajes directos</span>
    `;
    
    bubbleList.appendChild(notice);
    return notice;
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