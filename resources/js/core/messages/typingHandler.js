import { scrollToBottom } from './messageUtils.js';

export function initTypingBroadcast(convId, input) {
    if (!convId || !input) return;

    const currentUserId = parseInt(input.dataset.userId || '0');
    const currentUserName = input.dataset.userName || 'Usuario';
    const currentUserAvatar = input.dataset.userAvatar || '/img/default-avatar.png';

    let typingTimeout = null;
    const TYPING_TIMEOUT_MS = 3000;
    let isTyping = false;

    function broadcastTyping(typing) {
        if (!window.Echo) return;

        window.Echo.private(`messages.${convId}`)
            .whisper('typing', {
                conversation_id: convId,
                user_id: currentUserId,
                user_name: currentUserName,
                avatar_url: currentUserAvatar,
                is_typing: typing,
            });
    }

    input.addEventListener('input', () => {
        if (!isTyping) {
            isTyping = true;
            broadcastTyping(true);
        }

        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            isTyping = false;
            broadcastTyping(false);
        }, TYPING_TIMEOUT_MS);
    });

    input.addEventListener('blur', () => {
        if (isTyping) {
            isTyping = false;
            broadcastTyping(false);
        }
        clearTimeout(typingTimeout);
    });
}

export function showTypingIndicatorBelowLastMessage(userName, avatarUrl) {
    const bubbleList = document.getElementById('msgsBubbleList');
    if (!bubbleList) return;

    removeTypingIndicator();

    const bubbles = bubbleList.querySelectorAll('.msgs-bubble-wrap');
    if (bubbles.length === 0) return;

    const lastBubble = bubbles[bubbles.length - 1];

    const indicator = document.createElement('div');
    indicator.id = 'typingIndicator';
    indicator.className = 'msgs-typing-indicator msgs-typing-below';
    indicator.style.display = 'flex';

    indicator.innerHTML = `
        <img src="${escapeHtml(avatarUrl || '/img/default-avatar.png')}"
             alt="" class="msgs-typing-avatar">
        <div class="msgs-typing-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <span class="msgs-typing-text">${escapeHtml(userName)} está escribiendo...</span>
    `;

    lastBubble.after(indicator);
    scrollToBottom(bubbleList, true);
}

export function removeTypingIndicator() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) {
        indicator.remove();
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str ?? '')));
    return div.innerHTML;
}