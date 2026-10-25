import { scrollToBottom, autosizeInput, escapeHtml } from './messageUtils.js';
import { createOwnBubble, updateBubbleStatus, ensureDateSeparator, showNotAcceptingMessages } from './messageRenderer.js';
import { sendMessage } from './messageService.js';
import { removeTypingIndicator } from './typingHandler.js';
import { relativeTime } from '../../shared/relativeTime.js';

let timeUpdateInterval = null;

export function updateConvPreview(convId, previewText) {
    const convList = document.getElementById('msgsConvList');
    if (!convList || !convId) return;

    const existingItem = convList.querySelector('a[href*="conv=' + convId + '"]');
    if (existingItem) {
        const preview = existingItem.querySelector('.msgs-conv-preview');
        if (preview) {
            preview.textContent = previewText;
        }
        const time = existingItem.querySelector('.msgs-conv-time');
        if (time) {
            time.textContent = 'Ahora';
            time.dataset.timestamp = Date.now();
        }
        convList.prepend(existingItem);
    }
}

function updateConversationTimes() {
    const convList = document.getElementById('msgsConvList');
    if (!convList) return;

    const times = convList.querySelectorAll('.msgs-conv-time');

    times.forEach((timeEl) => {
        const timestamp = parseInt(timeEl.dataset.timestamp);
        if (!timestamp) return;

        timeEl.textContent = relativeTime(timestamp);
    });
}

export function startTimeUpdates() {
    const convList = document.getElementById('msgsConvList');
    if (!convList) return;

    updateConversationTimes();
    timeUpdateInterval = setInterval(updateConversationTimes, 1000);
}

export function stopTimeUpdates() {
    if (timeUpdateInterval) {
        clearInterval(timeUpdateInterval);
        timeUpdateInterval = null;
    }
}

export async function handleSendMessage({ input, sendBtn, bubbleList, syncSendBtn }) {
    if (!input || !sendBtn) return;

    const body = input.value.trim();
    if (!body) return;

    const convId = sendBtn.dataset.convId;
    const recipient = sendBtn.dataset.recipient;
    if (!convId && !recipient) return;

    sendBtn.disabled = true;
    input.disabled = true;

    const now = new Date();
    const dateKey = now.toLocaleDateString('en-CA', { timeZone: 'America/Bogota' });
    const tempBubble = createOwnBubble(body, 'sending', now.toISOString());
    if (bubbleList) {
        ensureDateSeparator(bubbleList, dateKey);
        bubbleList.appendChild(tempBubble);
        scrollToBottom(bubbleList, true);
    }

    const convList = document.getElementById('msgsConvList');
    if (convList && convId) {
        const existingItem = convList.querySelector('a[href*="conv=' + convId + '"]');
        if (existingItem) {
            const preview = existingItem.querySelector('.msgs-conv-preview');
            if (preview) {
                preview.textContent = 'Tú: ' + (body.length > 40 ? body.substring(0, 40) + '...' : body);
            }
            const time = existingItem.querySelector('.msgs-conv-time');
            if (time) {
                time.textContent = 'Ahora';
                time.dataset.timestamp = Date.now();
            }
            convList.prepend(existingItem);
        } else {
            const currentUser = window.currentUser || { id: 0, name: '', avatar_url: '' };
            const item = document.createElement('a');
            item.href = '/messages?conv=' + convId;
            item.className = 'msgs-conv-item';
            item.dataset.unread = 'false';
            item.dataset.type = 'individual';
            item.setAttribute('role', 'listitem');
            item.innerHTML =
                '<div class="msgs-conv-avatar-wrap">' +
                '<img src="' + escapeHtml(currentUser.avatar_url || '/img/default-avatar.png') + '" ' +
                'alt="' + escapeHtml(currentUser.name) + '" class="msgs-conv-avatar" ' +
                'onerror="this.src=\'/img/default-avatar.png\'">' +
                '</div>' +
                '<div class="msgs-conv-info">' +
                '<div class="msgs-conv-row-top">' +
                '<span class="msgs-conv-name">' + escapeHtml(currentUser.name) + '</span>' +
                '<span class="msgs-conv-time" data-timestamp="' + Date.now() + '">Ahora</span>' +
                '</div>' +
                '<div class="msgs-conv-row-bottom">' +
                '<span class="msgs-conv-preview">Tú: ' + escapeHtml(body.length > 40 ? body.substring(0, 40) + '...' : body) + '</span>' +
                '</div>' +
                '</div>';
            const firstItem = convList.querySelector('.msgs-conv-item');
            if (firstItem) {
                convList.insertBefore(item, firstItem);
            } else {
                convList.appendChild(item);
            }
            const emptySidebar = convList.querySelector('.msgs-empty-sidebar');
            if (emptySidebar) emptySidebar.remove();
        }
    }

    input.value = '';
    autosizeInput(input);

    try {
        const data = await sendMessage(body, convId, recipient);
        tempBubble.dataset.msgId = data.id ?? '';
        updateBubbleStatus(tempBubble, 'sent');
        removeTypingIndicator();

        // If recipient doesn't accept messages, show notice but keep message as sent
        if (data && data.recipient_accepts_messages === false) {
            showNotAcceptingMessages(bubbleList);
            if (sendBtn) sendBtn.disabled = true;
        }
    } catch (err) {
        console.error('[Fluxa Messages]', err);
        if (err.message === 'USER_NOT_ACCEPTING_MESSAGES') {
            showNotAcceptingMessages(bubbleList);
            if (sendBtn) sendBtn.disabled = true;
        } else {
            updateBubbleStatus(tempBubble, 'error');
        }
    }

    input.disabled = false;
    input.focus();
    syncSendBtn();
}

export function attachSendHandler(input, sendBtn, bubbleList, syncSendBtn) {
    if (input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!input.disabled) {
                    handleSendMessage({ input, sendBtn, bubbleList, syncSendBtn });
                }
            }
        });
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', () => {
            if (!input.disabled) {
                handleSendMessage({ input, sendBtn, bubbleList, syncSendBtn });
            }
        });
    }
}