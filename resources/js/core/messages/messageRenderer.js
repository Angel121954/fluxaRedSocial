import { escapeHtml, formatTime, formatFileSize } from './messageUtils.js';

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

export function createEditBtn() {
    const btn = document.createElement('button');
    btn.className = 'msgs-edit-btn';
    btn.setAttribute('aria-label', 'Editar mensaje');
    btn.setAttribute('title', 'Editar');
    btn.innerHTML = `
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
        </svg>
    `;
    return btn;
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

    bubble.appendChild(createEditBtn());

    const body = document.createElement('div');
    body.className = 'msgs-bubble-body';
    body.textContent = text;
    bubble.appendChild(body);

    const timeSpan = document.createElement('span');
    timeSpan.className = `msgs-bubble-time${status ? ' ' + status : ''}`;
    timeSpan.textContent = time;

    bubble.appendChild(timeSpan);

    const reactions = document.createElement('div');
    reactions.className = 'msgs-bubble-reactions';
    bubble.appendChild(reactions);

    const reactBtn = document.createElement('button');
    reactBtn.className = 'msgs-react-btn';
    reactBtn.setAttribute('aria-label', 'Reaccionar');
    reactBtn.innerHTML = `
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    `;
    bubble.appendChild(reactBtn);

    wrap.appendChild(bubble);
    return wrap;
}

export function updateBubbleBody(bubbleWrap, newBody, editedAt) {
    const bodyEl = bubbleWrap.querySelector('.msgs-bubble-body');
    if (bodyEl) {
        bodyEl.textContent = newBody;
    }

    const timeEl = bubbleWrap.querySelector('.msgs-bubble-time');
    if (timeEl && editedAt) {
        if (!timeEl.querySelector('.msgs-bubble-edited-label')) {
            const label = document.createElement('span');
            label.className = 'msgs-bubble-edited-label';
            label.textContent = '· editado';
            timeEl.appendChild(label);
        }
    }

    const bubble = bubbleWrap.querySelector('.msgs-bubble');
    if (bubble) {
        bubble.classList.add('msgs-bubble-edited');
    }
}

export function appendReceivedBubble(msg, bubbleList) {
    if (!bubbleList) return;

    const msgDate = msg.created_at
        ? msg.created_at.split('T')[0]
        : new Date().toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    ensureDateSeparator(bubbleList, msgDate);

    const wrap = document.createElement('div');
    wrap.className = 'msgs-bubble-wrap theirs';
    wrap.dataset.date = msgDate;
    wrap.dataset.msgId = msg.id ?? '';

    const avatar = document.createElement('img');
    avatar.src = escapeHtml(msg.sender?.avatar_url);
    avatar.alt = '';
    avatar.className = 'msgs-bubble-avatar';
    avatar.onerror = function () { this.src = '/img/default-avatar.png'; };
    wrap.appendChild(avatar);

    const bubble = document.createElement('div');
    bubble.className = 'msgs-bubble msgs-bubble-theirs';

    const body = document.createElement('div');
    body.className = 'msgs-bubble-body';
    body.textContent = msg.body ?? '';
    bubble.appendChild(body);

    const timeSpan = document.createElement('span');
    timeSpan.className = 'msgs-bubble-time';
    timeSpan.textContent = formatTime(msg.created_at);
    bubble.appendChild(timeSpan);

    const reactions = document.createElement('div');
    reactions.className = 'msgs-bubble-reactions';
    reactions.dataset.msgId = msg.id ?? '';
    bubble.appendChild(reactions);

    const reactBtn = document.createElement('button');
    reactBtn.className = 'msgs-react-btn';
    reactBtn.dataset.msgId = msg.id ?? '';
    reactBtn.setAttribute('aria-label', 'Reaccionar');
    reactBtn.innerHTML = `
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    `;
    bubble.appendChild(reactBtn);

    wrap.appendChild(bubble);
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

export function createOwnMediaBubble(mediaData, text, dateStr, status) {
    // Media bubbles cannot be edited, so no edit button
    const now = dateStr ? new Date(dateStr) : new Date();
    const time = now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'America/Bogota' });
    const dateKey = now.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    const wrap = document.createElement('div');
    wrap.className = 'msgs-bubble-wrap mine';
    wrap.dataset.date = dateKey;
    wrap.dataset.msgId = mediaData.id ?? '';

    const bubble = document.createElement('div');
    bubble.className = 'msgs-bubble msgs-bubble-mine msgs-bubble--media';

    if (mediaData.media_type === 'image' || mediaData.media_type === 'gif') {
        const imgWrap = document.createElement('div');
        imgWrap.className = 'msgs-media-img-wrap';
        if (mediaData.media_type === 'gif') imgWrap.classList.add('msgs-media-gif-wrap');
        const img = document.createElement('img');
        img.className = 'msgs-media-img';
        img.src = mediaData.media_url;
        img.alt = mediaData.media_name || 'Imagen';
        img.loading = 'lazy';
        imgWrap.appendChild(img);
        bubble.appendChild(imgWrap);
    } else {
        const fileCard = document.createElement('div');
        fileCard.className = 'msgs-media-file';
        fileCard.innerHTML = `
            <div class="msgs-media-file-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </div>
            <div class="msgs-media-file-info">
                <span class="msgs-media-file-name">${escapeHtml(mediaData.media_name || 'Archivo')}</span>
                <span class="msgs-media-file-size">${mediaData.media_size ? formatFileSize(mediaData.media_size) : ''}</span>
            </div>
        `;
        bubble.appendChild(fileCard);
    }

    if (text) {
        const body = document.createElement('div');
        body.className = 'msgs-bubble-body';
        body.textContent = text;
        bubble.appendChild(body);
    }

    const timeSpan = document.createElement('span');
    timeSpan.className = 'msgs-bubble-time' + (status ? ' ' + status : '');
    timeSpan.textContent = time;
    bubble.appendChild(timeSpan);

    const reactions = document.createElement('div');
    reactions.className = 'msgs-bubble-reactions';
    bubble.appendChild(reactions);

    const reactBtn = document.createElement('button');
    reactBtn.className = 'msgs-react-btn';
    reactBtn.setAttribute('aria-label', 'Reaccionar');
    reactBtn.innerHTML = `
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    `;
    bubble.appendChild(reactBtn);

    wrap.appendChild(bubble);
    return wrap;
}

export function appendReceivedMediaBubble(msg, bubbleList) {
    if (!bubbleList) return;

    const msgDate = msg.created_at
        ? msg.created_at.split('T')[0]
        : new Date().toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });

    ensureDateSeparator(bubbleList, msgDate);

    const wrap = document.createElement('div');
    wrap.className = 'msgs-bubble-wrap theirs';
    wrap.dataset.date = msgDate;
    wrap.dataset.msgId = msg.id ?? '';

    const avatar = document.createElement('img');
    avatar.src = escapeHtml(msg.sender?.avatar_url);
    avatar.alt = '';
    avatar.className = 'msgs-bubble-avatar';
    avatar.onerror = function () { this.src = '/img/default-avatar.png'; };
    wrap.appendChild(avatar);

    const bubble = document.createElement('div');
    bubble.className = 'msgs-bubble msgs-bubble-theirs msgs-bubble--media';

    if (msg.media_type === 'image' || msg.media_type === 'gif') {
        const imgWrap = document.createElement('div');
        imgWrap.className = 'msgs-media-img-wrap';
        if (msg.media_type === 'gif') imgWrap.classList.add('msgs-media-gif-wrap');
        const img = document.createElement('img');
        img.className = 'msgs-media-img';
        img.src = msg.media_url;
        img.alt = msg.media_name || 'Imagen';
        img.loading = 'lazy';
        imgWrap.appendChild(img);
        bubble.appendChild(imgWrap);
    } else {
        const fileCard = document.createElement('div');
        fileCard.className = 'msgs-media-file';
        fileCard.innerHTML = `
            <div class="msgs-media-file-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </div>
            <div class="msgs-media-file-info">
                <span class="msgs-media-file-name">${escapeHtml(msg.media_name || 'Archivo')}</span>
                <span class="msgs-media-file-size">${msg.media_size ? formatFileSize(msg.media_size) : ''}</span>
            </div>
        `;
        bubble.appendChild(fileCard);
    }

    if (msg.body) {
        const body = document.createElement('div');
        body.className = 'msgs-bubble-body';
        body.textContent = msg.body;
        bubble.appendChild(body);
    }

    const timeSpan = document.createElement('span');
    timeSpan.className = 'msgs-bubble-time';
    timeSpan.textContent = formatTime(msg.created_at);
    bubble.appendChild(timeSpan);

    const reactions = document.createElement('div');
    reactions.className = 'msgs-bubble-reactions';
    reactions.dataset.msgId = msg.id ?? '';
    bubble.appendChild(reactions);

    const reactBtn = document.createElement('button');
    reactBtn.className = 'msgs-react-btn';
    reactBtn.dataset.msgId = msg.id ?? '';
    reactBtn.setAttribute('aria-label', 'Reaccionar');
    reactBtn.innerHTML = `
        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    `;
    bubble.appendChild(reactBtn);

    wrap.appendChild(bubble);
    bubbleList.appendChild(wrap);
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